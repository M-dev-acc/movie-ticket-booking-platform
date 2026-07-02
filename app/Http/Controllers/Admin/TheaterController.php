<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Theater\{
    StoreTheaterRequest,
    UpdateTheaterRequest
};
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
     * Store a newly created resource in storage.
     */
    public function store(StoreTheaterRequest $request): JsonResponse
    {
        $theater = Theater::create($request->validated());
        return $this->created(new TheaterResource($theater), message: "Theater added successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Theater $theater)
    {
        return $this->success(new TheaterResource($theater), message: "Theater details");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTheaterRequest $request, Theater $theater): JsonResponse
    {
        $theater->update($request->validated());
        return $this->success(
            new TheaterResource($theater->fresh()),
            message: "Theater update successfully."
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theater $theater): JsonResponse
    {
        $theater->delete();

        return $this->noContent("Theater deleted successfully.");
    }
}
