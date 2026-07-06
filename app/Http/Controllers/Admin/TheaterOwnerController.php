<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Theater\TheaterResource;
use App\Models\{
    Theater,
    User,
};
use App\Services\Theater\TheaterOwnerService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TheaterOwnerController extends Controller
{
    use ApiResponse;

    public function __construct(
        public TheaterOwnerService $service = new TheaterOwnerService(),
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Theater $theater)
    {
        $theaterOwner = $theater->load('owners');

        return $this->success(
            data:  new TheaterResource($theaterOwner),
            message: "Theater owner details"
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Theater $theater): JsonResponse
    {
        $this->service->assign($theater, $request->input('user_id'));
        return $this->success([], message: "Owner added to theater successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theater $theater, User $user): JsonResponse
    {
        $this->service->revoke($theater, $user->id);
        return $this->noContent(message: "Owner removed from the theater");
    }
}
