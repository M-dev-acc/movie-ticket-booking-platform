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
        DB::beginTransaction();
        try {
            $showSeats = $this->getShowSeatsById(
                $inputs['show_id'],
                data_get($inputs, 'seats.*.id'));

            $data = $this->formatBookingData($inputs['show_id'], $showSeats);
            $booking = Booking::create($data);

            $this->lockRequestedSeats($showSeats);

        } catch (\Exception $error) {
            DB::rollBack();
            throw $error;
        }
        DB::commit();

        return $booking;
    }

    private function lockRequestedSeats(EloquentCollection $showSeats): void
    {
        ShowSeat::whereIn('id', $showSeats->pluck('id')->toArray())
            ->update([
                'status' => 'locked',
                'locked_until' => now()->addMinutes(15),
            ]);
    }

    public function getShowSeatsById(int $showId, array $seatIds): EloquentCollection
    {
        return ShowSeat::select(['id', 'price', 'seat_id'])
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
            ->findOrFail($showId);

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
        $prices = $seats->pluck('price')->toArray();
        $total = array_sum($prices);

        return $total;
    }
}
