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
     * Display the specified resource.
     */
    public function show(MovieShow $movieShow): JsonResponse
    {
        return $this->success(
            data: new MovieShowResource($movieShow),
            message: "Movie show details",
        );
    }
}
