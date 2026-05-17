<?php

namespace App\Http\Controllers;

use App\Http\Requests\Theater\StoreTheaterRequest;
use App\Http\Requests\Theater\UpdateTheaterRequest;
use App\Http\Resources\Theater\TheaterResource;
use App\Repositories\Interfaces\TheaterRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TheaterController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected TheaterRepositoryInterface $repository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $list = $this->repository->all();

        return $this->paginated(
            TheaterResource::collection($list),
            "Theater list"
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTheaterRequest $request): JsonResponse
    {
        $theater = $this->repository->create($request->validated());
        return $this->created(new TheaterResource($theater), message: "Theater added successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $theater = $this->repository->find($id);
        return $this->success(new TheaterResource($theater), message: "Theater details");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTheaterRequest $request, int $id): JsonResponse
    {
        $theater = $this->repository->update($id, $request->validated());

        return $this->success(new TheaterResource($theater), message: "Theater update successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);

        return $this->noContent("Theater deleted successfully.");
    }
}
