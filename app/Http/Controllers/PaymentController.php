<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\PaymentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(
        private PaymentService $service,
        private PaymentGatewayInterface $gateway
    ) {
    }

    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'booking_code' => 'required|string|exists:bookings,code',
        ]);

        $booking = Booking::where('code', $request->booking_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        if($booking->status !== Booking::STATUS_RESERVED) {
            return $this->error(
                message: 'This booking is not in payable state. Status: ' . $booking->status,
                statusCode: 422
            );
        }

        $orderData = $this->service->initiate($booking);

        return $this->success(
            data: $orderData,
            message: 'Payment order created successfullly.');
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $result = $this->gateway->verifyPaymentSignature(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );
        if (!$result['status']) {
            return $this->error(
                statusCode: 422,
                message:'Payment verification failed'
            );
        }

        return $this->success(
            data: null,
            message: 'Payment verification successful.',
        );
    }
}
