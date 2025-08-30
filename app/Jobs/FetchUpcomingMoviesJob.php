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

    protected string $languageCode;

    public function __construct(string $languageCode)
    {
        $this->languageCode = $languageCode;
    }

    public function handle(): void
    {
        // Resolve repository here instead of injecting in constructor
        $repository = app(MovieRepository::class);

        $apiResponse = $repository->getUpcoming($this->languageCode)->getData();

        if (!$apiResponse || !$apiResponse->status || empty($apiResponse->data->results)) {
            return;
        }

        $filteredList = $this->processResults($apiResponse->data->results);

        if ($filteredList->isNotEmpty()) {
            Movie::insert(
                $filteredList->toArray(),
                ['uniqueid'],
                ['title', 'poster', 'release_date', 'genres', 'original_language', 'updated_at']
            );
        }
    }

    private function processResults(array $results)
    {
        $ids = Movie::select('id')
            ->whereBetween('created_at', [
                today()->startOfDay(),
                today()->endOfDay()
            ])
            ->pluck('id')
            ->toArray();

        return collect($results)
            ->reject(fn($item) => in_array($item->id, $ids))
            ->map(fn($item) => [
                'uniqueid' => $item->id,
                'title' => $item->title ?? '',
                'poster' => $item->poster_path ?? null,
                'release_date' => $item->release_date ?? null,
                'genres' => json_encode($item->genre_ids ?? []),
                'original_language' => $item->original_language ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values();
    }
}
