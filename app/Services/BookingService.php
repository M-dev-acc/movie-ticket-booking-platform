<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\MovieShow;
use App\Models\Seat;
use App\Models\ShowSeat;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function areSeatsAvailable(array $ids) : bool {
        return ShowSeat::whereIn('id', $ids)
            ->where('status', 'available')
            ->exists();
    }

    public function formatBookingData(array $inputs) : array {
        $show = MovieShow::with('movie')
            ->where('id', $inputs['show_id'])
            ->get()
            ->first();
        return collect($inputs)
            ->put('user_id', auth()->id())
            ->put('movie_id', $show->movie->id)
            ->toArray();
    }

    public function formatBookingSeatsData(array $seats, Booking $booking) : array {
        # Check and Calculate paid amount here.

        return collect($seats)
            ->map(function ($seat) use($booking) {
                return [
                    'user_id' => auth()->id(),
                    'seat_id' => $seat->id,
                    'booking_id' => $booking->id,
                ];
            })
            ->toArray();
    }
}

