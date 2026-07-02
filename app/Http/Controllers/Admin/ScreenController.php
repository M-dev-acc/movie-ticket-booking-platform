<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Screen\{
    StoreScreenRequest,
    UpdateScreenRequest
};
use App\Http\Resources\Screen\ScreenResource;
use App\Models\Screen;
use App\Models\Theater;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ScreenController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Theater $theater): JsonResponse
    {
        $list = Screen::latest()
            ->without('theaters')
            ->when($theater->id, fn($query) => $query->where('theater_id', $theater->id))
            ->paginate(20);

        return $this->paginated(
            $list,
            resourceClass: ScreenResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Theater $theater, StoreScreenRequest $request): JsonResponse
    {
        $inputs = $request->validated();
        $inputs['theater_id'] = $theater->id;
        $screen = Screen::create($inputs);
        return $this->success(new ScreenResource($screen), message:"New Screen is added.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Theater $theater, Screen $screen): JsonResponse
    {
        return $this->success(new ScreenResource($screen), message: "Screen details");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScreenRequest $request, Theater $theater, Screen $screen): JsonResponse
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
    public function destroy(Theater $theater, Screen $screen): JsonResponse
    {
        $screen->delete();

        return $this->noContent("Screen deleted successfully.");
    }
}
