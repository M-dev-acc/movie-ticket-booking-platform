<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Theater\StoreTheaterRequest;
use App\Http\Requests\Theater\UpdateTheaterRequest;
use App\Http\Resources\Theater\TheaterCollection;
use App\Http\Resources\Theater\TheaterResource;
use App\Repositories\TheaterRepository;
use Illuminate\Http\JsonResponse;

class TheaterController extends Controller
{
    protected $repository;

    public function __construct(TheaterRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(int $page): JsonResponse | TheaterCollection
    {
        $list = $this->repository->all($page);
        if (!empty($list)) {

            // return (new TheaterCollection($list))
            //     ->additional([
            //         'status' => true,
            //         'message' => 'Theater list',
            //     ]);

            return new TheaterCollection($list);
        }

        return ApiResponse::error(message: "Data not found", status: 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTheaterRequest $request): JsonResponse
    {
        $theater = $this->repository->create($request->validated());
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
    public function show(string $id): JsonResponse
    {
        $theater = $this->repository->find($id);
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
    public function update(UpdateTheaterRequest $request, int $id): JsonResponse
    {
        $theater = $this->repository->update($id, $request->validated());

        return ApiResponse::success(
            new TheaterResource($theater),
            "Data added successfully!"
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);

        return ApiResponse::success(
            message: "Data deleted!"
        );
    }
}
