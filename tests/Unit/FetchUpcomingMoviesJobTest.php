<?php

// Bug fix: moved to Tests\Feature namespace — this test hits the DB
// via RefreshDatabase and belongs in tests/Feature/, not tests/Unit/
namespace Tests\Feature;

use App\DTOs\MovieDTO;
use App\Jobs\FetchUpcomingMoviesJob;
use App\Models\Movie;
use App\Repositories\Contracts\MovieRepositoryInterface;
use App\Services\ExternalApi\Contracts\MovieApiInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FetchUpcomingMoviesJobTest extends TestCase
{
    use RefreshDatabase;

    // ── DTO mapping ───────────────────────────────────────────────────────────

    /** @test */
    public function it_maps_tmdb_response_correctly_via_dto(): void
    {
        $raw = $this->sampleMovie();
        $dto = MovieDTO::fromTmdb($raw);

        $this->assertEquals('1552794', $dto->externalId);
        $this->assertEquals('Example title 1', $dto->title);
        $this->assertEquals('/AlxxkFRBPoPrTphNm0nAEYtmr96.jpg', $dto->posterPath);
        $this->assertEquals('hi', $dto->originalLanguage);
    }

    // ── Job handles paginated results ─────────────────────────────────────────

    /** @test */
    public function it_upserts_all_movies_from_api_response(): void
    {
        $mockApi = $this->mock(MovieApiInterface::class);
        $mockApi->shouldReceive('fetchUpcoming')
            ->once()
            ->with(page: 1, language: 'hi') // Bug fix: language must match job's languageCode
            ->andReturn($this->fakeApiResponse());

        $job = new FetchUpcomingMoviesJob('hi');
        $job->handle(
            api:  $mockApi,
            repo: $this->app->make(MovieRepositoryInterface::class),
        );

        $this->assertDatabaseCount('movies', 5);
        $this->assertDatabaseHas('movies', ['external_id' => '1552794', 'title' => 'Example title 1']);
        $this->assertDatabaseHas('movies', ['external_id' => '1486860', 'title' => 'Example title 2']);
        $this->assertDatabaseHas('movies', ['external_id' => '1227739', 'title' => 'Example title 3']);
        $this->assertDatabaseHas('movies', ['external_id' => '1339952', 'title' => 'Example Title 4']);
        $this->assertDatabaseHas('movies', ['external_id' => '1524457', 'title' => 'Example title 5']);
    }

    /** @test */
    public function it_stops_when_api_returns_empty_results(): void
    {
        $mockApi = $this->mock(MovieApiInterface::class);
        $mockApi->shouldReceive('fetchUpcoming')
            ->once()
            ->with(page: 1, language: 'hi')
            ->andReturn(['results' => [], 'total_pages' => 1]);

        $job = new FetchUpcomingMoviesJob('hi');
        $job->handle(
            api:  $mockApi,
            repo: $this->app->make(MovieRepositoryInterface::class),
        );

        $this->assertDatabaseCount('movies', 0);
    }

    /** @test */
    public function it_skips_and_logs_a_movie_that_fails_to_upsert(): void
    {
        $mockApi = $this->mock(MovieApiInterface::class);

        // Bug fix: null title will cause a TypeError inside MovieDTO::fromTmdb()
        // because $title is typed as non-nullable string.
        // The job catches \Throwable per movie, logs the warning, and continues —
        // so the good movie should still be stored.
        $badMovie  = array_merge($this->sampleMovie(id: 999), ['title' => null]);
        $goodMovie = $this->sampleMovie(id: 888, title: 'Good movie');

        $mockApi->shouldReceive('fetchUpcoming')
            ->once()
            ->with(page: 1, language: 'hi')
            ->andReturn(['results' => [$badMovie, $goodMovie], 'total_pages' => 1]);

        $job = new FetchUpcomingMoviesJob('hi');
        $job->handle(
            api:  $mockApi,
            repo: $this->app->make(MovieRepositoryInterface::class),
        );

        // Good movie should be saved despite the bad one throwing
        $this->assertDatabaseHas('movies', ['external_id' => '888', 'title' => 'Good movie']);
        // Bad movie should not exist
        $this->assertDatabaseMissing('movies', ['external_id' => '999']);
    }

    /** @test */
    public function it_passes_language_code_to_api(): void
    {
        $mockApi = $this->mock(MovieApiInterface::class);

        // Explicitly verifies the languageCode constructor arg flows through to the API call
        $mockApi->shouldReceive('fetchUpcoming')
            ->once()
            ->with(page: 1, language: 'en')
            ->andReturn(['results' => [], 'total_pages' => 1]);

        $job = new FetchUpcomingMoviesJob('en');
        $job->handle(
            api:  $mockApi,
            repo: $this->app->make(MovieRepositoryInterface::class),
        );
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function sampleMovie(int $id = 1552794, string $title = 'Example title 1'): array
    {
        return [
            'id'                => $id,
            'title'             => $title,
            'original_title'    => $title,
            'original_language' => 'hi',
            'overview'          => '',
            'poster_path'       => '/AlxxkFRBPoPrTphNm0nAEYtmr96.jpg',
            'backdrop_path'     => null,
            'release_date'      => '2025-09-26',
            'popularity'        => 0.0071,
            'vote_average'      => 0,
            'vote_count'        => 0,
            'adult'             => false,
            'genre_ids'         => [],
        ];
    }

    private function fakeApiResponse(): array
    {
        return [
            'page'          => 1,
            'total_pages'   => 1,
            'total_results' => 5,
            'results'       => [
                $this->sampleMovie(1552794, 'Example title 1'),
                $this->sampleMovie(1486860, 'Example title 2'),
                $this->sampleMovie(1227739, 'Example title 3'),
                $this->sampleMovie(1339952, 'Example Title 4'),
                $this->sampleMovie(1524457, 'Example title 5'),
            ],
        ];
    }
}
