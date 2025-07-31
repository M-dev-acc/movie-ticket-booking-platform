<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;

class MoviesAPIClient {
    protected Client $client;
    protected string $baseUrl;
    protected string $apiKey; 

    public function __construct() {
        $this->client = new Client();
        $this->baseUrl = config('services.movies_db.base_url');
        $this->apiKey = config('services.movies_db.api_key');
    }

    private function run(string $endpoint, string $method = 'GET', array $headers = []) {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'accept' => 'application/json',
            ],
        ];

        if (!empty($headers)) {
            $options['headers'] += $headers;
        }

        return $this->parseResponse($this->client
            ->request($method, $this->baseUrl . "$endpoint", $options));
    }

    private function parseResponse(ResponseInterface $response) {
        $content = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()
                ->json([
                        'status' => false,
                        'message' => "Invalid response"
                    ],
                    500);
        }

        return response()
            ->json([
                    'status' => ($response->getStatusCode() === 200),
                    'data' => $content
                ],
                $response->getStatusCode());
    }

    public function get(string $endpoint, array $request = [], array $headers = []) {
        if (!empty($request)) {
            $endpoint .= "?" . http_build_query($request);
        }

        return $this->run($endpoint, 'GET', $headers);
    }

    public function check() {
        return $this->run("authentication", "GET");
    }
}