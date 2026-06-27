<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Theater\{
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
}
