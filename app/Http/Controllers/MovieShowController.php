<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieShow\StoreMovieShowRequest;
use App\Http\Requests\MovieShow\UpdateMovieShowRequest;
use App\Http\Resources\MovieShow\MovieShowResource;
use App\Models\MovieShow;
use App\Repositories\Contracts\MovieShowRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class MovieShowController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected MovieShowRepositoryInterface $repository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->paginated(
            $this->repository->all(),
            "Movie Show list"
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovieShowRequest $request)
    {
        $movieShow = $this->repository->create($request->validated());
        return $this->success(
            data: new MovieShowResource($movieShow),
            message: "Movie show added successfully!",
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(MovieShow $movieShow)
    {
        return $this->success(
            data: new MovieShowResource($movieShow),
            message: "Movie show details",
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovieShowRequest $request, MovieShow $movieShow)
    {
        $movieShow = $this->repository->update($movieShow, $request->validated());
        return $this->success(
            data: new MovieShowResource($movieShow),
            message: "Movie details update successfully!",
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MovieShow $movieShow)
    {
        $this->repository->delete($movieShow);

        return $this->noContent("Movie show deleted successfully!");
    }
}
