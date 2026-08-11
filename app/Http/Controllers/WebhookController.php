<?php

namespace App\Http\Controllers;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private PaymentService $service,
    ) {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $rawPayload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        if (!$this->gateway->verifySignature($rawPayload, $signature)) {
            return response('Invalid signature.', 400);
        }

        $payload = json_decode($rawPayload, true);
        $event = $payload['event'] ?? null;

        try {
            match ($event) {
                'payment.captured' => $this->service->confirmFromWebhook($payload),
                'payment.failed' => $this->service->handleFailure($payload),
                default => null
            };
        } catch (\Throwable $th) {
            Log::warning('Webhook processing error.', [
               'event' => $event,
               'error' => $th->getMessage()
            ]);
        }

        return response()->noContent();
    }
}
