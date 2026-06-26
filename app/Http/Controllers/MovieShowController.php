<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieShow\{
    StoreMovieShowRequest,
    UpdateMovieShowRequest
};
use App\Http\Resources\MovieShow\MovieShowResource;
use App\Models\MovieShow;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class MovieShowController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $list =  MovieShow::latest()
            ->paginate(20);

        return $this->paginated(
            $list,
            "Movie Shows list"
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovieShowRequest $request): JsonResponse
    {
        $movieShow = MovieShow::create($request->validated());
        return $this->success(
            data: new MovieShowResource($movieShow),
            message: "Movie show added successfully!",
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(MovieShow $movieShow): JsonResponse
    {
        return $this->success(
            data: new MovieShowResource($movieShow),
            message: "Movie show details",
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovieShowRequest $request, MovieShow $movieShow): JsonResponse
    {
        $movieShow->update($request->validated());
        return $this->success(
            data: new MovieShowResource($movieShow->fresh()),
            message: "Movie details update successfully!",
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MovieShow $movieShow): JsonResponse
    {
        $movieShow->delete();

        return $this->noContent("Movie show deleted successfully!");
    }
}
