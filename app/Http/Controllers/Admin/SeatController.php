<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    Theater,
    Screen,
    Seat
};
use App\Http\Requests\Seat\{
    StoreSeatRequest,
    UpdateSeatRequest,
};
use App\Http\Resources\Seat\SeatResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Theater $theater, Screen $screen): JsonResponse
    {
        $seatsCollection = $screen->seats()
            ->orderBy('row')
            ->orderBy('number')
            ->without('screen')
            ->paginate(20);

        return $this->paginated(
            $seatsCollection,
            resourceClass: SeatResource::class
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSeatRequest $request, Theater $theater, Screen $screen): JsonResponse
    {
        $now = now();
        $seats = collect($request->validated('seats'))
            ->map(fn ($seat) => [
                ...$seat,
                'screen_id' => $screen->id,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->toArray();

        Seat::insert($seats);

        return $this->success(
            data: [],
            message: count($seats) . " seats created for screen.",
            statusCode: 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Theater $theater, Screen $screen, Seat $seat): JsonResponse
    {
        return $this->success(new SeatResource($seat), message: "Seat details");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSeatRequest $request, Theater $theater, Screen $screen, Seat $seat)
    {
        // dd($request->validated());
        $seat->update($request->validated());
        $seat->refresh();

        return $this->success(
            new SeatResource($seat),
            message: "Seat details updated successfully!"
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theater $theater, Screen $screen, Seat $seat): JsonResponse
    {
        $seat->delete();
        return $this->noContent("Seat deleted successfully!");
    }
}
