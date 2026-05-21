<?php
namespace App\Services\ExternalApi\Http;

use App\Exceptions\ApiAuthException;

class ApiAuthenticator
{
    public function __construct(
        private readonly string $apiKey
    ){}

    public function getHeaders() : array {
        return [
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json',
        ];
    }

    public function forceRefresh() : void {
        throw new ApiAuthException(
            'TMDB API key was rejected (401). Check TMDB_API_KEY in your .env file.'
        );
    }
}

