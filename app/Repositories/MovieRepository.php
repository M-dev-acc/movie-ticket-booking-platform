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
        $request = [
            'include_adult' => 'true',
            'include_video' => 'false',
            'page' => 1,
            'primary_release_year' => today()->format('Y'),
            'primary_release_date.gte' => today()->previousWeekendDay()->format('Y-m-d'),
            'primary_release_date.lte' => today()->format('Y-m-d'),
            'region' => "ISO 3166-1",
            'sort_by' => "primary_release_date.desc",
            'vote_average.gte' => 4,
            'vote_average.lte' => 10,
            'with_original_language' => $language,
            'with_release_type' => '3,2',
            'year' => today()->format('Y'),
        ];
        return $this->apiClient->get("discover/movie", $request);
    }

    public function getUpcoming(string $language) {
        $request = [
            'include_adult' => 'true',
            'include_video' => 'false',
            'page' => 1,
            'primary_release_year' => today()->format('Y'),
            'primary_release_date.gte' => today()->format('Y-m-d'),
            'primary_release_date.lte' => today()->addDays(7)->format('Y-m-d'),
            'region' => "ISO 3166-1",
            'sort_by' => "primary_release_date.desc",
            'with_original_language' => $language,
            'with_release_type' => 3,
        ];

        return $this->apiClient->get("discover/movie", $request);
    }

    public function getById(string $id) {
        $apiEndPoint = "movie/$id";
        return $this->apiClient->get($apiEndPoint);
    }
}
