<?php

namespace Tests\Feature;

use App\Jobs\FetchUpcomingMoviesJob;
use App\Repositories\MovieRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class FetchUpcomingMoviesJobTest extends TestCase
{
    /** @test */
    public function it_can_fetch_movies_from_api()
    {
        $repository = app(MovieRepository::class);
        $moviesData = $repository->getUpcoming('en', 1);

        $this->assertIsArray($moviesData);
        $this->assertArrayHasKey('results', $moviesData);
        $this->assertNotEmpty($moviesData['results']);

        // Optional: check fields exist
        $firstMovie = $moviesData['results'][0];
        $this->assertArrayHasKey('title', $firstMovie);
        $this->assertArrayHasKey('release_date', $firstMovie);
    }

    /** @test */
    public function it_processes_api_response_correctly()
    {
        $job = new FetchUpcomingMoviesJob('en');

        $sampleApiResponse = $this->fakeResponse();

        $processed = $job->processResults($sampleApiResponse['data']['results']);

        $this->assertNotEmpty($processed);
        $this->assertEquals('Example title 1', $processed[0]['title']);

        // You can assert that movies with missing data are filtered
        $this->assertCount(5, $processed); // or whatever your filter logic requires
    }


    /** @test */
    public function it_stores_movies_correctly_in_database()
    {
        $this->assertDatabaseCount('movies', 0);

        $moviesToStore = collect($this->fakeResponse());

        \App\Models\Movie::upsert(
            $moviesToStore->toArray(),
            ['uniqueid'],
            ['title', 'release_date', 'rating']
        );

        $this->assertDatabaseHas('movies', ['uniqueid' => 1552794, 'title' => "Example title 1"]);
        $this->assertDatabaseHas('movies', ['uniqueid' => 1486860, 'title' => "Example title 2"]);
        $this->assertDatabaseHas('movies', ['uniqueid' => 1227739, 'title' => "Example title 3"]);
        $this->assertDatabaseHas('movies', ['uniqueid' => 1339952, 'title' => "Example Title 4"]);
        $this->assertDatabaseHas('movies', ['uniqueid' => 1524457, 'title' => "Example title 5"]);
    }

    /**
     * Return fake API response
     */
    private function fakeResponse() : array {
        return [
            "status" => true,
            "data" => [
                "page"=> 1,
                "results"=>  [
                    [
                        "adult" => false,
                        "backdrop_path" => null,
                        "genre_ids" => [],
                        "id" => 1552794,
                        "original_language" => "hi",
                        "original_title" => "Example title 1",
                        "overview" => "",
                        "popularity" => 0.0071,
                        "poster_path" => "/AlxxkFRBPoPrTphNm0nAEYtmr96.jpg",
                        "release_date" => "2025-09-26",
                        "title" => "Example title 1",
                        "video" => false,
                        "vote_average" => 0,
                        "vote_count" => 0,
                    ],
                    [
                        "adult" => false,
                        "backdrop_path" => null,
                        "genre_ids" =>  [
                        27
                        ],
                        "id" => 1486860,
                        "original_language" => "hi",
                        "original_title" => "Example title 2",
                        "overview" => "Sequel to the 2011 Indian horror.",
                        "popularity" => 2.0811,
                        "poster_path" => null,
                        "release_date" => "2025-09-26",
                        "title" => "Example title 2",
                        "video" => false,
                        "vote_average" => 0,
                        "vote_count" => 0,
                    ],
                    [
                        "adult" => false,
                        "backdrop_path" => "/v9w0xds8GUzOwHTZRuw2yeObRzD.jpg",
                        "genre_ids" =>  [
                        18
                        ],
                        "id" => 1227739,
                        "original_language" => "hi",
                        "original_title" => "Example title 3",
                        "overview" => "Two childhood friends from a small North Indian village chase a police job that promises them the dignity they’ve long been denied.",
                        "popularity" => 3.7447,
                        "poster_path" => "/vyezjSvSdLO0bvr6jSNuFi6yuiw.jpg",
                        "release_date" => "2025-09-26",
                        "title" => "Example title 3",
                        "video" => false,
                        "vote_average" => 0,
                        "vote_count" => 0,
                    ],
                    [
                        "adult" => false,
                        "backdrop_path" => null,
                        "genre_ids" =>  [
                        35,
                        10749,
                        18,
                        ],
                        "id" => 1339952,
                        "original_language" => "hi",
                        "original_title" => "Example Title 4",
                        "overview" => "",
                        "popularity" => 1.415,
                        "poster_path" => null,
                        "release_date" => "2025-09-25",
                        "title" => "Example Title 4",
                        "video" => false,
                        "vote_average" => 0,
                        "vote_count" => 0,
                    ],
                    [
                        "adult" => false,
                        "backdrop_path" => null,
                        "genre_ids" =>  [
                        10749
                        ],
                        "id"=> 1524457,
                        "original_language" => "hi",
                        "original_title" => "Example title 5",
                        "overview" => "Example title 5 Is A Journey Of Love , Acceptance And Self-Discovery",
                        "popularity" => 0.0764,
                        "poster_path" => "/lpFxReyBf9CFxWrorE1DjsgL6bh.jpg",
                        "release_date" => "2025-09-22",
                        "title" => "Example title 5",
                        "video" => false,
                        "vote_average" => 0,
                        "vote_count" => 0,
                    ]
                ],
                "total_pages"=> 1,
                "total_results"=> 5,
            ]
        ];
    }
}
