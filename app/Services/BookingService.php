<?php

namespace App\Services;

use App\Models\Seat;
use App\Models\ShowSeat;

class BookingService
{
    public function areSeatsAvailable(array $ids) : bool {
        return ShowSeat::whereIn('id', $ids)
            ->where('status', 'available')
            ->exists();
    }

    public function areSeatsFromSameTheater(array $ids, int $theater) : bool {
        return Seat::query()
            ->join('screens', 'screens.id', '=', 'seats.screen_id')
            ->where('screens.theater_id', $theater)
            ->whereIn('seats.id', $ids)
            ->exists();
    }
}

