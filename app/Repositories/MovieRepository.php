<?php

namespace App\Repositories;

use App\Repositories\Interfaces\MoviesRepositoryInterface;
use App\Services\MoviesAPIClient;

class MovieRepository implements MoviesRepositoryInterface
{
    public function __construct(
        protected MoviesAPIClient $apiClient = new MoviesAPIClient()
    ) {}

    public function getLatestRelease(string $language) {
        $today = today();

        $request = [
            'include_adult' => 'true',
            'include_video' => 'false',
            'page' => 1,
            'primary_release_year' => $today->year(),
            'primary_release_date.gte' => $today->previousWeekendDay()->format('Y-m-d'),
            'primary_release_date.lte' => $today->format('Y-m-d'),
            'region' => "ISO 3166-1",
            'sort_by' => "popularity.desc",
            'vote_average.gte' => 4,
            'vote_average.lte' => 10,
            'with_original_language' => $language,
            'with_release_type' => 3,
            'year' => $today->year(),
        ];
        $req = $this->apiClient->get("discover/movie", $request);
        return $req;
    }

    public function getUpcoming(string $language) {
        $today = today();

        $request = [
            'include_adult' => 'true',
            'include_video' => 'false',
            'page' => 1,
            'primary_release_year' => $today->year(),
            'primary_release_date.gte' => $today->nextWeekendDay()->format('Y-m-d'),
            'primary_release_date.lte' => $today->format('Y-m-d'),
            'region' => "ISO 3166-1",
            'sort_by' => "popularity.desc",
            'vote_average.gte' => 4,
            'vote_average.lte' => 10,
            'with_original_language' => $language,
            'with_release_type' => 3,
            'year' => $today->year(),
        ];
        
        return $this->apiClient->get("discover/movie", $request);
    }

    public function getById(string $id) {
        $apiEndPoint = "movie/$id";
        return $this->apiClient->get($apiEndPoint);
    }
}
