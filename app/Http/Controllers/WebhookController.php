<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Payment;

class WebhookController extends Controller
{
    /**
     * Handle incoming payment webhook
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        Log::info('Payment webhook received', ['payload' => $payload]);

        // Validate webhook signature (provider-specific)
        if (!$this->validateWebhookSignature($request)) {
            Log::warning('Invalid webhook signature', ['payload' => $payload]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $payload['event'] ?? $payload['type'] ?? null;
        
        switch ($event) {
            case 'payment.success':
            case 'payment_intent.succeeded':
                return $this->handlePaymentSuccess($payload);
                
            case 'payment.failed':
            case 'payment_intent.payment_failed':
                return $this->handlePaymentFailed($payload);
                
            case 'refund.completed':
            case 'charge.refunded':
                return $this->handleRefund($payload);
                
            default:
                Log::info('Unhandled webhook event', ['event' => $event]);
                return response()->json(['message' => 'Event received']);
        }
    }

    /**
     * Validate webhook signature
     */
    private function validateWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-Webhook-Signature');
        $webhookSecret = config('services.payment.webhook_secret');
        
        if (!$webhookSecret) {
            // Skip validation if no secret configured (dev mode)
            return true;
        }

        $expectedSignature = hash_hmac('sha256', $request->getContent(), $webhookSecret);
        
        return hash_equals($expectedSignature, $signature ?? '');
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentSuccess(array $payload)
    {
        $paymentId = $payload['data']['payment_id'] ?? $payload['data']['object']['id'] ?? null;
        $orderId = $payload['data']['order_id'] ?? $payload['data']['object']['metadata']['order_id'] ?? null;

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order && $order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'paid_at' => now()
                ]);

                Log::info('Order marked as paid via webhook', ['order_id' => $orderId]);
            }
        }

        if ($paymentId) {
            Payment::where('transaction_id', $paymentId)->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }

        return response()->json(['message' => 'Payment processed']);
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailed(array $payload)
    {
        $orderId = $payload['data']['order_id'] ?? $payload['data']['object']['metadata']['order_id'] ?? null;
        $error = $payload['data']['error'] ?? $payload['data']['object']['last_payment_error']['message'] ?? 'Payment failed';

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                    'payment_error' => $error
                ]);

                Log::warning('Payment failed via webhook', [
                    'order_id' => $orderId,
                    'error' => $error
                ]);
            }
        }

        return response()->json(['message' => 'Payment failure recorded']);
    }

    /**
     * Handle refund
     */
    private function handleRefund(array $payload)
    {
        $orderId = $payload['data']['order_id'] ?? $payload['data']['object']['metadata']['order_id'] ?? null;

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update([
                    'payment_status' => 'refunded',
                    'refunded_at' => now()
                ]);

                Log::info('Order refunded via webhook', ['order_id' => $orderId]);
            }
        }

        return response()->json(['message' => 'Refund processed']);
    }
}
