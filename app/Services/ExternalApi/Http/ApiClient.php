<?php

use App\Services\ExternalApi\Http\ApiAuthenticator;
use App\Exceptions\ApiAuthException;
use App\Exceptions\ApiConnectionException;
use App\Exceptions\ApiRateLimitException;
use Illuminate\Support\Facades\Http as FacadesHttp;
use Illuminate\Support\Facades\Log;
use League\Uri\Http;

class ApiClient
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MS = 500;
    private const TIMEOUT_SEC = 10;

    public function __construct(
        private readonly ApiAuthenticator $auth,
        private readonly string $baseUrl
    ){}

    private function withRetry(callable $request) : array {
        $attempts = 0;

        while(true){
            try {
                $attempts++;
                return $request();
            } catch (ApiAuthException $th) {
                if ($attempts > 1) {
                    throw $th;
                }

                Log::warning('Api Auth failed, attempting refresh', [
                    'attempts' => $attempts,
                    'error' => $th->getMessage()
                ]);

                $this->auth->forceRefresh();
            } catch (ApiRateLimitException $th){
                
            }
        }

        return [];
    }

    private function makeRequest(string $method, string $endpoint, string $params) : array {
        $response = FacadesHttp::withHeaders($this->auth->getHeaders())
            ->timeout(self::TIMEOUT_SEC)
            ->method("{$this->baseUrl}{$endpoint}");

        return match (true) {
            $response->ok() => $response->json,

            $response->status() === 401 => throw new ApiAuthException(
                'API returned Unauthorized.'
            ),

            $response->status() === 429 => throw new ApiRateLimitException(
                retryAfterMs: (int) ($response->header('Retry-After') ?? 60) * 1000,
            ),

            $response->status() === 404 => throw new ApiConnectionException(
                'Server error {$response->status()} at {$endpoint}.'
            ),

            $response->serverError() => throw new ApiConnectionException(
                'Unexpected response {$response->status()} from {$endpoint}.'
            ),
        };
    }
}

