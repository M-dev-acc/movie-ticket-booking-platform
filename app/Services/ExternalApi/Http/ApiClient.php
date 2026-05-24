<?php
namespace App\Services\ExternalApi\Http;

use App\Services\ExternalApi\Http\ApiAuthenticator;
use App\Exceptions\ApiAuthException;
use App\Exceptions\ApiConnectionException;
use App\Exceptions\ApiRateLimitException;
use Illuminate\Support\Facades\Http as FacadesHttp;
use Illuminate\Support\Facades\Log;

class ApiClient
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MS = 500;
    private const TIMEOUT_SEC = 10;

    public function __construct(
        private readonly ApiAuthenticator $auth,
        private readonly string $baseUrl
    ){}

    public function get(string $endpoint, array $params = []) : array {
        return $this->withRetry(fn() =>
            $this->makeRequest('get', $endpoint, $params)
        );
    }

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
                Log::info('API rate limited, wait before retry.', [
                    'retry_after_ms' => $th->retryAfterMs,
                ]);

                throw $th;
            } catch (ApiConnectionException $th) {
                if($attempts >= self::MAX_RETRIES){
                    Log::error('API Connection failed after max retries.', [
                        'attempts' => $attempts,
                        'error' => $th->getMessage(),
                    ]);

                    throw $th;
                }

                $delay = self::RETRY_DELAY_MS * (2 ** ($attempts -1));

                Log::warning('API Connnection error, retrying.', [
                    'attempts' => $attempts,
                    'delay_ ms' => $delay,
                    'error' => $th->getMessage(),
                ]);

                $this->sleepFor($delay);
            }
        }
    }

    private function makeRequest(string $method, string $endpoint, array $params) : array {
        $response = FacadesHttp::withHeaders($this->auth->getHeaders())
            ->timeout(self::TIMEOUT_SEC)
            ->$method("{$this->baseUrl}{$endpoint}", $params);

        return match (true) {
            $response->ok() => $response->json(),

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

    private function sleepFor(int $milliseconds) : void {
        usleep($milliseconds * 1000);
    }
}

