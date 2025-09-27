<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class MoviesAPIClient
{
    protected Client $client;
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10, // fail fast
        ]);
        $this->baseUrl = rtrim(config('services.movies_db.base_url'), '/');
        $this->apiKey = config('services.movies_db.api_key');
    }

    /**
     * Run an API request
     *
     * @throws \RuntimeException
     */
    private function run(string $endpoint, string $method = 'GET', array $options = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        $defaultHeaders = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ];

        $options['headers'] = array_merge($defaultHeaders, $options['headers'] ?? []);

        try {
            $response = $this->client->request($method, $url, $options);
            return $this->parseResponse($response);
        } catch (RequestException $e) {
            throw new \RuntimeException(
                "Movies API request failed: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Parse the Guzzle response
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $content = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON response from Movies API");
        }

        return [
            'status' => $response->getStatusCode() == 200,
            'status_code' => $response->getStatusCode(),
            'data' => $content,
        ];
    }

    /**
     * Perform GET request
     */
    public function get(string $endpoint, array $query = [], array $headers = []): array
    {
        return $this->run($endpoint, 'GET', [
            'query' => $query,
            'headers' => $headers,
        ]);
    }

    /**
     * Simple check if API is alive
     */
    public function check(): array
    {
        return $this->get('authentication');
    }
}
