<?php

namespace App\Services\Payment\Contracts;

use App\Models\Booking;

interface PaymentGatewayInterface
{
    public function createOrder(Booking $booking): array;

    public function verifySignature(string $payload, string $signature): bool;

    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): array;

}
