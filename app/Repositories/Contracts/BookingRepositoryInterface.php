<?php

namespace  App\Repositories\Contracts;

use App\Models\Booking;
use Illuminate\Pagination\LengthAwarePaginator;

interface BookingRepositoryInterface {
    public function all(int $perPage = 20): LengthAwarePaginator;

    public function find(int $id): Booking;

    public function create(array $data): Booking;

    public function update(Booking $booking, array $data): Booking;

    public function delete(Booking $booking): ?bool;
}
