<?php

namespace App\Services\Payment;

use App\Models\Booking;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Razorpay\Api\Api;

class RazorpayService implements PaymentGatewayInterface
{
    public function createOrder(Booking $booking): array
    {
        return [];

    }

    public function verifySignature(string $payload, string $signature): bool
    {
        return false;
    }

    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): array
    {
        return [];
    }
}

