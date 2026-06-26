<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\{
    StoreBookingRequest,
    UpdateBookingRequest
};
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $bookings = Booking::latest()
            ->paginate(20);
        return $this->success($bookings, message: 'Bookings list');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $bookingDetails = Booking::create($request->validated());
        return $this->success($bookingDetails, message: 'Show booked successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking): JsonResponse
    {
        return $this->success($booking, message: 'Booking details');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $booking->update($request->validated());

        return $this->success($booking->fresh(), message: 'Booking details updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return $this->noContent('Booking cancelled successfully');
    }
}
