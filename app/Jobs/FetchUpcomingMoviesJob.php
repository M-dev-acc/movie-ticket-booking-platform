<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Repositories\MovieRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchUpcomingMoviesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $languageCode;

    public function __construct(string $languageCode)
    {
        $this->languageCode = $languageCode;
    }

    public function handle(): void
    {
        $repository = app(MovieRepository::class);
        $page = 1;

        do {
            $moviesData = $repository->getUpcoming($this->languageCode, $page);

            if (empty($moviesData['results'])) {
                break;
            }
            $filteredList = $this->processResults($moviesData['results']);
            if ($filteredList->isNotEmpty()) {
                Movie::upsert(
                    $filteredList->toArray(),
                    ['uniqueid'],
                    ['title', 'poster', 'release_date', 'updated_at', 'rating']
                );
            }

            $page++;
        } while ($page <= $moviesData['total_pages']);
    }

    public function processResults(array $results)
    {
        $ids = Movie::select('id')
            ->whereBetween('created_at', [
                today()->startOfDay(),
                today()->endOfDay()
            ])
            ->pluck('id')
            ->toArray();
        return collect($results)
            ->reject(fn($item) => in_array($item['id'], $ids))
            ->map(fn($item) => [
                'uniqueid' => $item['id'],
                'title' => $item['title'] ?? '',
                'poster' => $item['poster_path'] ?? '',
                'release_date' => $item['release_date'] ?? '',
                'genres' => json_encode($item['genre_ids'] ?? []),
                'original_language' => $item['original_language'] ?? '',
                'rating' => $item['vote_average'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values();
    }
}
