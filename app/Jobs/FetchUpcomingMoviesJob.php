<?php

namespace App\Jobs;

use App\DTOs\MovieDTO;
use App\Repositories\Contracts\MovieRepositoryInterface;
use App\Services\ExternalApi\Contracts\MovieApiInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchUpcomingMoviesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $languageCode,
    ) {}

    /**
     * Laravel resolves MovieApiInterface and MovieRepositoryInterface
     * from the container automatically when the job is handled.
     */
    public function handle(
        MovieApiInterface        $api,
        MovieRepositoryInterface $repo,
    ): void {
        $page = 1;

        do {
            try {
                // Bug fix: languageCode is now passed to the API — was previously ignored
                $response = $api->fetchUpcoming(page: $page, language: $this->languageCode);
            } catch (\Throwable $e) {
                Log::error('FetchUpcomingMoviesJob: API call failed.', [
                    'page'     => $page,
                    'language' => $this->languageCode,
                    'error'    => $e->getMessage(),
                ]);
                break;
            }

            $results = $response['results'] ?? [];

            if (empty($results)) {
                break;
            }

            // MovieDTO::fromTmdb() is the single source of truth for TMDB field mapping.
            // No raw field names here — if TMDB changes a field, only the DTO needs updating.
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

            $totalPages = $response['total_pages'] ?? 1;
            $page++;

        } while ($page <= $totalPages);
    }
}
