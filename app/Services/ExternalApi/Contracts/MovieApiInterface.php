<?php

namespace App\Services\ExternalApi\Contracts;

interface MovieApiInterface
{
    /**
     * Fetch a single movie by its TMDB external ID.
     *
     * @throws \App\Exceptions\ApiAuthException        On 401 - key rejected
     * @throws \App\Exceptions\ApiConnectionException  On 5xx or timeout
     * @throws \App\Exceptions\ApiRateLimitException   On 429 - rate limited
     */
    public function fetchMovie(string $externalId): array;

    /**
     * Search movies by a query string.
     *
     * @throws \App\Exceptions\ApiAuthException
     * @throws \App\Exceptions\ApiConnectionException
     * @throws \App\Exceptions\ApiRateLimitException
     */
    public function searchMovies(string $query, int $page = 1): array;

    /**
     * Fetch movies currently playing in theatres.
     *
     * @throws \App\Exceptions\ApiAuthException
     * @throws \App\Exceptions\ApiConnectionException
     * @throws \App\Exceptions\ApiRateLimitException
     */
    public function fetchNowPlaying(int $page = 1): array;

    /**
     * Fetch movies sorted by popularity.
     *
     * @throws \App\Exceptions\ApiAuthException
     * @throws \App\Exceptions\ApiConnectionException
     * @throws \App\Exceptions\ApiRateLimitException
     */
    public function fetchPopular(int $page = 1): array;

    /**
     * Fetch upcoming movies.
     * Bug fix: language parameter added so callers (e.g. FetchUpcomingMoviesJob)
     * can filter results by language code (e.g. 'hi', 'en').
     *
     * @throws \App\Exceptions\ApiAuthException
     * @throws \App\Exceptions\ApiConnectionException
     * @throws \App\Exceptions\ApiRateLimitException
     */
    public function fetchUpcoming(int $page = 1, string $language = 'en'): array;

    /**
     * Fetch cast and crew credits for a movie.
     *
     * @throws \App\Exceptions\ApiAuthException
     * @throws \App\Exceptions\ApiConnectionException
     * @throws \App\Exceptions\ApiRateLimitException
     */
    public function fetchCredits(string $externalId): array;
}
