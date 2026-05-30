<?php

namespace App\Http\Controllers;

use App\Http\Requests\Screen\StoreScreenRequest;
use App\Http\Requests\Screen\UpdateScreenRequest;
use App\Http\Resources\Screen\ScreenResource;
use App\Models\Screen;
use App\Repositories\Contracts\ScreenRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ScreenController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ScreenRepositoryInterface $repository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(?int $theaterId = null)
    {
        $list = $this->repository->all($theaterId);

        return $this->paginated($list);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScreenRequest $request)
    {
        $screen = $this->repository->create($request->validated());
        return $this->success(new ScreenResource($screen), message:"New Screen is added.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Screen $screen)
    {
        return $this->success(new ScreenResource($screen), message: "Screen details");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScreenRequest $request, Screen $screen)
    {
        $updatedScreen = $this->repository->update($screen, $request->validated());
        return $this->success(new ScreenResource($updatedScreen), message:"Screen details are updated.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Screen $screen)
    {
        $this->repository->delete($screen);

        return $this->noContent("Screen deleted successfully.");
    }
}
