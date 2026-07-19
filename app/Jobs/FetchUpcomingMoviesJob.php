<?php

namespace App\Jobs;

use App\DTOs\MovieDTO;
use App\Repositories\Contracts\MovieRepositoryInterface;
use App\Services\ExternalApi\Contracts\MovieApiInterface;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchUpcomingMoviesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];
    public int $timeout = 300;
    public int $maxExceptions = 3;
    public int $uniqueFor = 3600;

    public function __construct(
        public readonly string $languageCode,
    ) {}

    public function uniqueId(): string
    {
        return $this->languageCode;
    }

    public function handle(
        MovieApiInterface        $api,
        MovieRepositoryInterface $repo,
    ): void {
        $page = 1;
        $totalPages = 1;

        do {
            $response = $api->fetchUpcoming(page: $page, language: $this->languageCode);

            $results = $response['results'] ?? [];

            if (empty($results)) {
                break;
            }

            foreach ($results as $raw) {
                try {
                    $dto = MovieDTO::fromTmdb($raw);
                    $repo->upsert($dto->toArray());
                } catch (\Throwable $e) {
                    Log::warning('FetchUpcomingMoviesJob: failed to upsert a movie.', [
                        'tmdb_id' => $raw['id'] ?? 'unknown',
                        'error'   => $e->getMessage(),
                    ]);
                }
            }

            $totalPages = (int) ($response['total_pages'] ?? 1);
            $page++;

            if ($page <= $totalPages) {
                usleep(250_000); // respect TMDB rate limits between pages
            }

        } while ($page <= $totalPages);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('FetchUpcomingMoviesJob: job failed permanently.', [
            'language' => $this->languageCode,
            'error'    => $exception->getMessage(),
        ]);
        // Consider notifying/alerting here — this is a full-run failure, not a per-item one
    }
}
