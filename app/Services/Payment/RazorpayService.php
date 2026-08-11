<?php

namespace App\Services\Payment;

use App\Models\Booking;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Razorpay\Api\Api as Razorpay;
use Razorpay\Api\Errors\SignatureVerificationError;
use Razorpay\Api\Order as RazorpayOrder;

class RazorpayService implements PaymentGatewayInterface
{
    public function __construct(
        private Razorpay $gateway
    ) {
    }

    /**
     * Create a Razorpay order.
     * Docs: https://razorpay.com/docs/api/orders/create/
     *
     * Returns the full Razorpay order object as an array.
     * The frontend needs: id (gateway_order_id), amount, currency.
     */
    public function createOrder(Booking $booking): RazorpayOrder
    {
        return (object) $this->gateway->order->create([
            'amount' => (int) ($booking->total_amount * 100), // amount in paise (100 paise = 1 rupee)
            'currency' => 'INR',
            'receipt' => $booking->code,
        ]);
    }

    /**
     * Verify the webhook signature.
     * Docs: https://razorpay.com/docs/webhooks/validate-test/
     *
     * Razorpay signs the raw JSON body with your webhook_secret using
     * HMAC-SHA256 and puts the result in X-Razorpay-Signature header.
     *
     * hash_equals() is required — not ===.
     * It uses constant-time comparison to prevent timing attacks.
     */
    public function verifySignature(string $payload, string $signature): bool
    {
        $expected = hash_hmac('sha256', $payload, config('razorpay.webhook.secret'));
        return hash_equals($expected, $signature);
    }

    /**
     * Verify the frontend payment signature.
     * Docs: https://razorpay.com/docs/payments/server-integration/php/build-integration/#14-verify-payment-signature
     *
     * After the Razorpay modal closes, the frontend receives three values.
     * This method verifies they were not tampered with.
     */
    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): array
    {
        try {
            $this->gateway
                ->utility->verifyPaymentSignature([
                    'razorpay_order_id' => $orderId,
                    'razorpay_payment_id' => $paymentId,
                    'razorpay_signature' => $signature
                ]);
            return [
                'status' => true,
            ];
        } catch (SignatureVerificationError $th) {
            return [
                'status' => false,
                'error' => $th->getMessage(),
            ];
        }

    }
}
