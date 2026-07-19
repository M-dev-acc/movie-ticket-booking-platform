<?php

namespace App\Repositories;

use App\Models\Movie;
use App\Repositories\Contracts\MovieRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MovieRepository implements MovieRepositoryInterface
{
    // ── Single record ─────────────────────────────────────────────────────────

    public function findById(int $id): ?Movie
    {
        return Movie::find($id);
    }

    public function findByExternalId(string $externalId): ?Movie
    {
        return Movie::where('external_id', $externalId)->first();
    }

    // ── Write ─────────────────────────────────────────────────────────────────

    /**
     * Insert or update by uniqueid.
     * Accepts output of MovieDTO::toArray() directly.
     */
    public function upsert(array $data): Movie
    {
        Movie::updateOrCreate(
            ['external_id' => $data['external_id']],
            $data
        );

        return Movie::where('external_id', $data['external_id'])->first();
    }

    // ── Listings ──────────────────────────────────────────────────────────────

    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return Movie::where('title', 'like', "%{$query}%")
            ->orWhere('overview', 'like', "%{$query}%")
            ->orderByDesc('popularity')
            ->paginate($perPage);
    }

    public function getLatest(int $perPage = 20): LengthAwarePaginator
    {
        return Movie::whereYear('release_date', now()->year)
            ->where('release_date', '<=', now()->toDateString())
            ->orderByDesc('release_date')
            ->paginate($perPage);
    }

    public function getUpcoming(int $perPage = 20): LengthAwarePaginator
    {
        return Movie::where('release_date', '>', now()->toDateString())
            ->orderBy('release_date')
            ->paginate($perPage);
    }

    public function getPopular(int $perPage = 20): LengthAwarePaginator
    {
        return Movie::orderByDesc('popularity')
            ->paginate($perPage);
    }

    public function getNowPlaying(int $perPage = 20): LengthAwarePaginator
    {
        return Movie::whereMonth('release_date', now()->month)
            ->whereYear('release_date', now()->year)
            ->orderByDesc('popularity')
            ->paginate($perPage);
    }

    // ── Staleness ─────────────────────────────────────────────────────────────

    public function isStale(string $externalId, int $hours = 24): bool
    {
        $movie = $this->findByExternalId($externalId);

        if (!$movie || is_null($movie->synced_at)) {
            return true;
        }

        return $movie->synced_at->diffInHours(now()) >= $hours;
    }
}
