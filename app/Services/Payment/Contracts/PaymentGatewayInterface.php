<?php

namespace App\Services\Payment\Contracts;

use App\Models\Booking;
use Razorpay\Api\Order as RazorpayOrder;

interface PaymentGatewayInterface
{
    public function createOrder(Booking $booking): RazorpayOrder;

    public function verifySignature(string $payload, string $signature): bool;

    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): array;

}
