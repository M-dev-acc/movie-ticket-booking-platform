<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingRepository implements BookingRepositoryInterface
{
    public function all(int $perPage = 20): LengthAwarePaginator {
        return Booking::latest()
            ->paginate($perPage);
    }

    public function find(int $id): Booking {
        return Booking::findOrFail($id);
    }

    public function create(array $data): Booking {
        return Booking::create($data);
    }

    public function update(Booking $booking, array $data): Booking {
        $booking->update($data);

        return $booking->fresh();
    }

    public function delete(Booking $booking): ?bool {
        return $booking->delete();
    }
}

