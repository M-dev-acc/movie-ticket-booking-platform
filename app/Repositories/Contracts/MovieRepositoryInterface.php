<?php

namespace App\Repositories\Contracts;

use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MovieRepositoryInterface
{
    public function findById(int $id): ?Movie;

    public function findByExternalId(string $externalId): ?Movie;

    /**
     * Insert or update by external_id.
     * Accepts output of MovieDTO::toArray() directly.
     */
    public function upsert(array $data): Movie;

    public function search(string $query, int $perPage = 20): LengthAwarePaginator;

    public function getLatest(int $perPage = 20): LengthAwarePaginator;

    public function getUpcoming(int $perPage = 20): LengthAwarePaginator;

    public function getPopular(int $perPage = 20): LengthAwarePaginator;

    public function getNowPlaying(int $perPage = 20): LengthAwarePaginator;

    public function isStale(string $externalId, int $hours = 24): bool;
}
