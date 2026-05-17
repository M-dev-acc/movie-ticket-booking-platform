<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Repositories\Interfaces\MoviesRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected MoviesRepositoryInterface $repository
    ) {}

    /**
     * Returns a list of latest movies
     */
    public function index(int $page = 1) : JsonResponse
    {
        $movies = $this->repository->getLatestRelease(config('services.language_code.hindi'), $page);
        if (empty($movies['results'])) {
            return $this->notFound();
        }
        return $this->success(data: $movies, message: "Latest movies");
    }

    /**
     * Return the detials of specific movie.
     */
    public function show(int $id) : JsonResponse
    {
        $movie = $this->repository->getById($id);

        if (empty($movie)) {
            return $this->notFound();
        }
        return $this->success(data: $movie, message: "Latest movies");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function upcoming(int $page = 1)
   {
        $movies = $this->repository->getUpcoming(config('services.language_code.hindi'), $page);
        if (empty($movies['results'])) {
            return $this->notFound(message: "Data not found");
        }
        return $this->success(data: $movies, message: "Upcoming movies list");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'query' => "required|string|min:1|max:255|regex:/^[A-Za-z0-9() ]+$/s"
        ],
        [
            'query.required' => "Please enter valid search input",
            'query.string' => "Please enter valid search input",
            'query.min' => "Please enter valid search input",
            'query.regex' => "Please enter valid search input",
            'query.max' => "Please enter input below 255 characters",
        ]);

        $list = Movie::select([
            'title',
            'poster_path',
            'release_date',
            'generes',
            'original_language'
            ])
            ->where('title', "LIKE", "%". $validatedData['query'])
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        //
    }
}
