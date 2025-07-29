<?php

namespace App\Services;

use GuzzleHttp\Client;

final class MoviesDBClient {
    public function __construct(
        protected string $baseUrl = config('services.movies_db.base_url'),
        protected string $apiKey = config('services.movies_db.api_key'),
        protected $client = new Client()
    ) {}

    private function get(string $endpoint, string $method, array $headers = []) {
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'accept' => 'application/json',
        ];

        $response = $this->client->request('GET', $this->baseUrl . "/$endpoint", [
            'headers' => $headers,
        ]);

        return response()->json([
            'data' => $response->getBody()
        ], $response->getStatusCode());
    }

    private function check() {
        return $this->get("authentication", "GET");
    }
}