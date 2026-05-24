<?php

namespace App\Services;

use App\DTOs\MovieDTO;
use App\Exceptions\ApiAuthException;
use App\Exceptions\ApiConnectionException;
use App\Models\Movie;
use App\Repositories\Contracts\MovieRepositoryInterface;
use App\Services\ExternalApi\Contracts\MovieApiInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class MovieService
{
    public function __construct(
        private readonly MovieRepositoryInterface $repo,
        private readonly MovieApiInterface        $api,
    ) {}

    // ── Single movie ──────────────────────────────────────────────────────────

    /**
     * DB-first fetch by TMDB external ID.
     * Serves cached data if fresh; re-syncs from API if stale or missing.
     *
     * @throws \RuntimeException if movie is unavailable in both DB and API
     */
    public function getMovie(string $externalId): Movie
    {
        $movie = $this->repo->findByExternalId($externalId);

        if ($movie && !$this->repo->isStale($externalId)) {
            return $movie;
        }

        try {
            $data = $this->api->fetchMovie($externalId);
            return $this->repo->upsert(MovieDTO::fromTmdb($data)->toArray());

        } catch (ApiAuthException $e) {
            Log::critical('TMDB API key rejected.', ['error' => $e->getMessage()]);
            return $this->serveStaleFallback($movie, $e);

        } catch (ApiConnectionException $e) {
            Log::warning('TMDB unreachable, serving stale DB data.', ['error' => $e->getMessage()]);
            return $this->serveStaleFallback($movie, $e);
        }
    }

    // ── Listings ──────────────────────────────────────────────────────────────

    /**
     * Latest released movies — DB only.
     * Data is kept fresh by FetchUpcomingMoviesJob syncing regularly.
     */
    public function getLatest(int $perPage = 20): LengthAwarePaginator
    {
        return $this->repo->getLatest($perPage);
    }

    /**
     * Upcoming movies — DB first, API fallback if DB is empty.
     */
    public function getUpcoming(int $perPage = 20): LengthAwarePaginator
    {
        $results = $this->repo->getUpcoming($perPage);

        if ($results->isNotEmpty()) {
            return $results;
        }

        try {
            $apiResults = $this->api->fetchUpcoming();
            $this->syncMovies($apiResults['results'] ?? []);

        } catch (ApiAuthException | ApiConnectionException $e) {
            Log::warning('API upcoming fetch failed, returning empty DB results.', [
                'error' => $e->getMessage(),
            ]);
        }

        return $this->repo->getUpcoming($perPage);
    }

    /**
     * Popular movies — DB first, API fallback if DB is empty.
     */
    public function getPopular(int $perPage = 20): LengthAwarePaginator
    {
        $results = $this->repo->getPopular($perPage);

        if ($results->isNotEmpty()) {
            return $results;
        }

        try {
            $apiResults = $this->api->fetchPopular();
            $this->syncMovies($apiResults['results'] ?? []);

        } catch (ApiAuthException | ApiConnectionException $e) {
            Log::warning('API popular fetch failed.', ['error' => $e->getMessage()]);
        }

        return $this->repo->getPopular($perPage);
    }

    /**
     * Now playing — DB first, API fallback if DB is empty.
     */
    public function getNowPlaying(int $perPage = 20): LengthAwarePaginator
    {
        $results = $this->repo->getNowPlaying($perPage);

        if ($results->isNotEmpty()) {
            return $results;
        }

        try {
            $apiResults = $this->api->fetchNowPlaying();
            $this->syncMovies($apiResults['results'] ?? []);

        } catch (ApiAuthException | ApiConnectionException $e) {
            Log::warning('API now_playing fetch failed.', ['error' => $e->getMessage()]);
        }

        return $this->repo->getNowPlaying($perPage);
    }

    // ── Search ────────────────────────────────────────────────────────────────

    /**
     * Search DB only — data is assumed pre-synced by jobs.
     */
    public function searchMovies(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return $this->repo->search($query, $perPage);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Bulk upsert a raw API results array into the DB via DTO.
     */
    private function syncMovies(array $movies): void
    {
        foreach ($movies as $raw) {
            try {
                $this->repo->upsert(MovieDTO::fromTmdb($raw)->toArray());
            } catch (\Throwable $e) {
                Log::warning('Failed to sync a movie from API.', [
                    'tmdb_id' => $raw['id'] ?? 'unknown',
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Return stale cached data when API is unreachable.
     * Throws only when no cached data exists at all.
     *
     * @throws \RuntimeException
     */
    private function serveStaleFallback(?Movie $movie, \Throwable $cause): Movie
    {
        if ($movie) {
            return $movie;
        }

        throw new \RuntimeException(
            'Movie unavailable: API unreachable and no cached data exists.',
            previous: $cause,
        );
    }
}
