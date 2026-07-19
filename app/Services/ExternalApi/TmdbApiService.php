<?php

namespace App\Services\ExternalApi;

use App\Services\ExternalApi\Contracts\MovieApiInterface;
use App\Services\ExternalApi\Http\ApiClient;

class TmdbApiService implements MovieApiInterface
{
    public function __construct(
        private readonly ApiClient $client,
    ) {}

    public function fetchMovie(string $externalId): array
    {
        return $this->client->get("/movie/{$externalId}");
    }

    public function searchMovies(string $query, int $page = 1): array
    {
        return $this->client->get('/search/movie', [
            'query' => $query,
            'page'  => $page,
        ]);
    }

    public function fetchNowPlaying(int $page = 1): array
    {
        return $this->client->get('/movie/now_playing', ['page' => $page]);
    }

    public function fetchPopular(int $page = 1): array
    {
        return $this->client->get('/movie/popular', ['page' => $page]);
    }

    /**
     * Bug fix: added language parameter so FetchUpcomingMoviesJob
     * can filter results by language — was previously always defaulting to TMDB's default.
     */
    public function fetchUpcoming(int $page = 1, string $language = 'en'): array
    {
        return $this->client->get('discover/movie', [
            'primary_release_date.gte' => now()->format('Y-m-d'),
            // 'primary_release_date.lte' => now()->addDays(30)->format('Y-m-d'),
            'page'     => $page,
            'language' => $language,
            'region' => 'IN',
            'with_original_language' => $language,
            'with_release_type' => '2|3',
            'sort_by' => 'popularity.desc',
        ]);
    }

    public function fetchCredits(string $externalId): array
    {
        return $this->client->get("/movie/{$externalId}/credits");
    }
}
