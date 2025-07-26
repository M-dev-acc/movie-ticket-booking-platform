<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Theater\StoreTheaterRequest;
use App\Http\Requests\Theater\UpdateTheaterRequest;
use App\Http\Resources\Theater\TheaterResource;
use App\Repositories\TheaterRepository;

class TheaterController extends Controller
{
    protected $theaterRepository;

    public function __construct(TheaterRepository $theaterRepository) {
        $this->theaterRepository = $theaterRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $list = $this->theaterRepository->all();
        if (!empty($list)) {
            return ApiResponse::success(
                TheaterResource::collection($list),
                "Theater list"
            );
        }

        return ApiResponse::error(message: "Data not found", status: 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTheaterRequest $request)
    {
        $theater = $this->theaterRepository->create($request->validated());
        if (!is_null($theater)) {
            return ApiResponse::success(
                new TheaterResource($theater),
                "Data added successfully!"
            );
        }

        return ApiResponse::error(message: "Failed to save data", status: 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $theater = $this->theaterRepository->find($id);
        if ($theater) {
            return ApiResponse::success(
                new TheaterResource($theater),
                "Data found"
            );
        }

        return ApiResponse::error(message: "Data not found", status: 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTheaterRequest $request, int $id)
    {
        $theater = $this->theaterRepository->update($id, $request->validated());

        return ApiResponse::success(
            new TheaterResource($theater),
            "Data added successfully!"
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->theaterRepository->delete($id);

        return ApiResponse::success(
            message: "Data deleted!"
        );
    } 
}
