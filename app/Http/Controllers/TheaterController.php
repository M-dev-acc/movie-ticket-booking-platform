<?php

namespace App\Http\Controllers;

use App\Http\Resources\Theater\TheaterResource;
use App\Models\Theater;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TheaterController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $list = Theater::latest()->paginate(20);

        return $this->paginated(
            $list,
            "Theater list",
            resourceClass: TheaterResource::class
        );
    }
    /**
     * Display the specified resource.
     */
    public function show(Theater $theater)
    {
        return $this->success(new TheaterResource($theater), message: "Theater details");
    }
}
