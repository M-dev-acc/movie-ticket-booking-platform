<?php

namespace Tests\Feature;

use App\Jobs\FetchUpcomingMoviesJob;
use App\Repositories\MovieRepository;
use Doctrine\DBAL\Driver\FetchUtils;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Tests\TestCase;

class FetchUpcomingMoviesJobTest extends TestCase
{
    /**
     * @test
     * Test dispatchement of the block.
     */
    public function it_dispatches_the_fetch_movie_job(): void
    {
        Bus::fake();

        FetchUpcomingMoviesJob::dispatch('hi');

        Bus::assertDispatched(FetchUpcomingMoviesJob::class, function ($job) {
            return $job->languageCode == 'hi';
        });
    }

    /**
     * @test
     * Test the logic to fetch and store the api data
     */
    // public function test_it_fetches_and_saves_movies_from_api(): void
    // {
    //     $apiClientMock = Mockery::mock(\App\Services\MoviesAPIClient::class);
    //     $apiClientMock->shouldReceive('get')
    //         ->once()
    //         ->with('discover/movie', Mockery::on(fn($arg) => $arg['with_original_language'] === 'hi'))
    //         ->andReturn($this->fakeResponse());

    //     $this->app->instance(\App\Services\MoviesAPIClient::class, $apiClientMock);

    //     $job = new FetchUpcomingMoviesJob('hi');
    //     $job->handle();

    //     $this->assertDatabaseHas('movies', [
    //         'uniqueid' => 1552794,
    //         'title'    => 'Example title 1',
    //     ]);

    //     $this->assertDatabaseCount('movies', 2);
    // }


    /**
     * 
     * Return fake API response
     */
    // public function fakeResponse() : object {
    //     return (object) [
    //         "status" => true,
    //         "data" => [
    //             "page"=> 1,
    //             "results"=>  [
    //                 [
    //                     "adult" => false,
    //                     "backdrop_path" => null,
    //                     "genre_ids" => [],
    //                     "id" => 1552794,
    //                     "original_language" => "hi",
    //                     "original_title" => "Example title 1",
    //                     "overview" => "",
    //                     "popularity" => 0.0071,
    //                     "poster_path" => "/AlxxkFRBPoPrTphNm0nAEYtmr96.jpg",
    //                     "release_date" => "2025-09-26",
    //                     "title" => "Example title 1",
    //                     "video" => false,
    //                     "vote_average" => 0,
    //                     "vote_count" => 0,
    //                 ],
    //                 [
    //                     "adult" => false,
    //                     "backdrop_path" => null,
    //                     "genre_ids" =>  [
    //                     27
    //                     ],
    //                     "id" => 1486860,
    //                     "original_language" => "hi",
    //                     "original_title" => "Example title 2",
    //                     "overview" => "Sequel to the 2011 Indian horror.",
    //                     "popularity" => 2.0811,
    //                     "poster_path" => null,
    //                     "release_date" => "2025-09-26",
    //                     "title" => "Example title 2",
    //                     "video" => false,
    //                     "vote_average" => 0,
    //                     "vote_count" => 0,
    //                 ],
    //                 [
    //                     "adult" => false,
    //                     "backdrop_path" => "/v9w0xds8GUzOwHTZRuw2yeObRzD.jpg",
    //                     "genre_ids" =>  [
    //                     18
    //                     ],
    //                     "id" => 1227739,
    //                     "original_language" => "hi",
    //                     "original_title" => "Example title 3",
    //                     "overview" => "Two childhood friends from a small North Indian village chase a police job that promises them the dignity they’ve long been denied.",
    //                     "popularity" => 3.7447,
    //                     "poster_path" => "/vyezjSvSdLO0bvr6jSNuFi6yuiw.jpg",
    //                     "release_date" => "2025-09-26",
    //                     "title" => "Example title 3",
    //                     "video" => false,
    //                     "vote_average" => 0,
    //                     "vote_count" => 0,
    //                 ],
    //                 [
    //                     "adult" => false,
    //                     "backdrop_path" => null,
    //                     "genre_ids" =>  [
    //                     35,
    //                     10749,
    //                     18,
    //                     ],
    //                     "id" => 1339952,
    //                     "original_language" => "hi",
    //                     "original_title" => "Example Title 4",
    //                     "overview" => "",
    //                     "popularity" => 1.415,
    //                     "poster_path" => null,
    //                     "release_date" => "2025-09-25",
    //                     "title" => "Example Title 4",
    //                     "video" => false,
    //                     "vote_average" => 0,
    //                     "vote_count" => 0,
    //                 ],
    //                 [
    //                     "adult" => false,
    //                     "backdrop_path" => null,
    //                     "genre_ids" =>  [
    //                     10749
    //                     ],
    //                     "id"=> 1524457,
    //                     "original_language" => "hi",
    //                     "original_title" => "Example title 5",
    //                     "overview" => "Example title 5 Is A Journey Of Love , Acceptance And Self-Discovery",
    //                     "popularity" => 0.0764,
    //                     "poster_path" => "/lpFxReyBf9CFxWrorE1DjsgL6bh.jpg",
    //                     "release_date" => "2025-09-22",
    //                     "title" => "Example title 5",
    //                     "video" => false,
    //                     "vote_average" => 0,
    //                     "vote_count" => 0,
    //                 ]
    //             ],
    //             "total_pages"=> 1,
    //             "total_results"=> 5,
    //         ]
    //     ];
    // }
}
