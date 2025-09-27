<?php

namespace App\Http\Controllers;

use App\Jobs\FetchUpcomingMoviesJob;
use App\Models\Movie;
use App\Repositories\MovieRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MovieController extends Controller
{
    public function __construct(
        protected $repository = new MovieRepository()
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(int $page = 1) : JsonResponse
    {
        $moviesData = $this->repository->getLatestRelease(config('services.language_code.hindi'), $page);
        if (empty($moviesData['results'])) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => "Data not found!",
            ], 404);
        }
        return response()->json([
                'status' => true,
                'data' => $moviesData,
                'message' => "Upcoming movies list",
            ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id) : JsonResponse
    {
        $movieData = $this->repository->getById($id);

        if (empty($movieData)) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => "Data not found!",
            ], 404);
        }
        return response()->json([
                'status' => true,
                'data' => $movieData,
                'message' => "Upcoming movies list",
            ], 200);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function upcoming(int $page = 1) 
   {
        $moviesData = $this->repository->getUpcoming(config('services.language_code.hindi'), $page);
        if (empty($moviesData['results'])) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => "Data not found!",
            ], 404);
        }
        return response()->json([
                'status' => true,
                'data' => $moviesData,
                'message' => "Upcoming movies list",
            ], 200);
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
