<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Repositories\MovieRepository;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __construct(
        protected $repository = new MovieRepository()
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->repository->getLatestRelease(config('services.language_code.hindi'));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->repository->getById($id);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function upcoming()
    {
        return $this->repository->getUpcoming(config('services.language_code.hindi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
