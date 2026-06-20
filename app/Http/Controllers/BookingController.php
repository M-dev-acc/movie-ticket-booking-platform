<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Repositories\BookingRepository;
use App\Traits\ApiResponse;

class BookingController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected BookingRepository $repository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = $this->repository->all();
        return $this->success($bookings, 'Bookings list');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $bookingDetails = $this->repository->create($request->validated());
        return $this->success($bookingDetails, 'Show booked successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        return $this->success($booking, 'Booking details');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $booking = $this->repository->update($booking, $request->validated());

        return $this->success($booking, 'Booking details updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $this->repository->delete($booking);

        return $this->noContent('Booking cancelled successfully');
    }
}
