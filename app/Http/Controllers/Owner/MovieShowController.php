<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovieShow\{
    StoreMovieShowRequest,
    UpdateMovieShowRequest
};
use App\Http\Resources\MovieShow\MovieShowResource;
use App\Models\{
    MovieShow,
    Screen,
    Theater,
};
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class MovieShowController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Theater $theater, Screen $screen): JsonResponse
    {
        $this->authorize('viewAny',  MovieShow::class);

        $list =  $screen->shows()
            ->latest()
            ->paginate(20);

        return $this->paginated(
            paginator: $list,
            message: "Movie Shows list",
            resourceClass: MovieShowResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Theater $theater, Screen $screen, StoreMovieShowRequest $request): JsonResponse
    {
        $this->authorize('create', MovieShow::class);

        $movieShow = MovieShow::create($request->validated());
        return $this->success(
            data: new MovieShowResource($movieShow),
            message: "Movie show added successfully!",
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Theater $theater, Screen $screen, MovieShow $movieShow): JsonResponse
    {
        $movieShow = MovieShow::with('screen.theater')->findOrFail($movieShow->id);
        $this->authorize('view', $movieShow);

        return $this->success(
            data: new MovieShowResource($movieShow),
            message: "Movie show details",
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Theater $theater, Screen $screen, UpdateMovieShowRequest $request, MovieShow $movieShow): JsonResponse
    {
        $movieShow = MovieShow::with('screen.theater')->findOrFail($movieShow->id);
        $this->authorize('update', $movieShow);

        $movieShow->update($request->validated());
        return $this->success(
            data: new MovieShowResource($movieShow->fresh()),
            message: "Movie details update successfully!",
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theater $theater, Screen $screen, MovieShow $movieShow): JsonResponse
    {
        $movieShow = MovieShow::with('screen.theater')->findOrFail($movieShow->id);
        $this->authorize('delete', $movieShow);

        $movieShow->delete();

        return $this->noContent("Movie show deleted successfully!");
    }
}
