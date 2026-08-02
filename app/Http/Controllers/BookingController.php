<?php

namespace App\Http\Controllers;

use App\Http\Resources\Booking\BookingResource;
use App\Models\Booking;
use App\Http\Requests\{
    StoreBookingRequest,
    UpdateBookingRequest
};
use App\Services\BookingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    use ApiResponse;

    public function __construct(
        private BookingService $service
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $bookings = Booking::latest()
            ->where('user_id', auth()->id())
            ->paginate(20);
        return $this->success($bookings, message: 'Bookings list');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $this->authorize('create', Booking::class);

        $bookingDetails = $this->service->createBooking($request->validated());
        return $this->success(
            data: new BookingResource($bookingDetails),
            message: 'Show booked successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);

        return $this->success(
            new BookingResource($booking),
            message: 'Booking details');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        $booking->delete();

        return $this->noContent('Booking cancelled successfully');
    }
}
