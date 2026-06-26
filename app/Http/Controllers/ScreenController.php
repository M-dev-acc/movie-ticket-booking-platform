<?php

namespace App\Http\Controllers;

use App\Http\Requests\Screen\StoreScreenRequest;
use App\Http\Requests\Screen\UpdateScreenRequest;
use App\Http\Resources\Screen\ScreenResource;
use App\Models\Screen;
use App\Repositories\Contracts\ScreenRepositoryInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ScreenController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(?int $theaterId = null): JsonResponse
    {
        $list = Screen::latest()
            ->when($theaterId, fn($query) => $query->where('theater_id', $theaterId))
            ->paginate(20);

        return $this->paginated($list);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScreenRequest $request): JsonResponse
    {
        $screen = Screen::create($request->validated());
        return $this->success(new ScreenResource($screen), message:"New Screen is added.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Screen $screen): JsonResponse
    {
        return $this->success(new ScreenResource($screen), message: "Screen details");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScreenRequest $request, Screen $screen): JsonResponse
    {
        $screen->update($request->validated());
        return $this->success(
            new ScreenResource($screen->fresh()),
            message:"Screen details are updated."
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Screen $screen): JsonResponse
    {
        $screen->delete();

        return $this->noContent("Screen deleted successfully.");
    }
}
