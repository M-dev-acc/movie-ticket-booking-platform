<?php

namespace App\Http\Controllers;

use App\Http\Requests\Theater\StoreTheaterRequest;
use App\Http\Requests\Theater\UpdateTheaterRequest;
use App\Models\Theater;
use App\Repositories\TheaterRepository;
use Illuminate\Http\Request;

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
        return $this->theaterRepository->all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTheaterRequest $request)
    {
        $this->theaterRepository->create($request->validated());

        return response()->json([
            'status' => true,
            'message' => "Data saved successfully!!!"
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->theaterRepository->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTheaterRequest $request, int $id)
    {
        $this->theaterRepository->update($id, $request->validated());

        return response()->json([], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->theaterRepository->delete($id);

        return response()->json([], 200);
    } 
}
