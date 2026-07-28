<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\MovieShow;
use App\Models\ShowSeat;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function createBooking(array $inputs): Booking
    {
        $inputs = collect($inputs);
        DB::beginTransaction();
        try {
            $seats = $this->getShowSeatsById(
                $inputs->show_id,
                $inputs->pluck('seats.id')->toArray());

            $data = $this->formatBookingData($inputs->show_id, $seats);
            $booking = Booking::create($data);

            $this->lockRequestedSeats($booking->show_id, $seats);
        } catch (\Exception $error) {
            DB::rollBack();
            throw $error;
        }
        DB::commit();

        return $booking;
    }

    private function lockRequestedSeats(int $showId, EloquentCollection $seats): bool
    {
        $seatsData = $seats->map(function ($seat) use ($showId) {
                return [
                    'seat_id' => $seat->id,
                    'show_id' => $showId,
                    'price' => $seat->price,
                    'status' => 'locked',
                    'locked_until' => now()->addMinutes(15),
                ];
            })
            ->toArray();
        return ShowSeat::insert($seatsData);
    }

    public function getShowSeatsById(int $showId, array $seatIds): EloquentCollection
    {
        return ShowSeat::select(['id', 'price'])
            ->whereIn('seat_id', $seatIds)
            ->where('show_id', $showId)
            ->where('status', 'available')
            ->get();
    }

    public function areSeatsAvailable(int $showId, array $seatIds): bool
    {
        return ShowSeat::whereIn('seat_id', $seatIds)
            ->where('show_id', $showId)
            ->where('status', 'available')
            ->count() === count($seatIds);
    }

    private function formatBookingData(int $showId, EloquentCollection $seats): array
    {
        $show = MovieShow::with('movie')
            ->where('id', $showId)
            ->get()
            ->first();
        $totalAmount = $this->calculateTotalAmount($seats);

        return [
            'user_id' => auth()->id(),
            'show_id' => $show->id,
            'movie_id' => $show->movie->id,
            'total_amount' => $totalAmount,
        ];
    }

    private function calculateTotalAmount(EloquentCollection $seats): float
    {
        $prices = $seats->pluck('seats.id')->toArray();
        $total = array_sum($prices);

        return $total;
    }
}
