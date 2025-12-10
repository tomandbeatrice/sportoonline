<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripeService implements PaymentGatewayInterface
{
    protected $secretKey;
    protected $currency;

    public function __construct()
    {
        $this->secretKey = config('stripe.secret_key');
        $this->currency = config('stripe.currency');
        Stripe::setApiKey($this->secretKey);
    }

    /**
     * Stripe Checkout Session oluşturur
     */
    public function createCheckoutSession(Order $order)
    {
        try {
            $lineItems = $this->prepareLineItems($order);

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('payment.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.stripe.cancel'),
                'client_reference_id' => $order->id,
                'customer_email' => $order->user->email,
                'metadata' => [
                    'order_id' => $order->id,
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'order_id' => $order->id,
                    ],
                ],
            ]);

            // Transaction ID olarak session ID'yi kaydet
            $order->update(['transaction_id' => $session->id]);

            return [
                'session_id' => $session->id,
                'checkout_url' => $session->url,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe Checkout Session Error: ' . $e->getMessage());
            throw new \Exception('Stripe ödeme oturumu oluşturulamadı: ' . $e->getMessage());
        }
    }

    /**
     * Sipariş ürünlerini Stripe line items formatına çevirir
     */
    protected function prepareLineItems(Order $order)
    {
        $lineItems = [];

        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => $this->currency,
                    'product_data' => [
                        'name' => $item->product->name,
                        'description' => $item->product->description ?? '',
                        'images' => $item->product->image ? [asset('storage/' . $item->product->image)] : [],
                    ],
                    'unit_amount' => intval($item->price * 100), // Kuruş/cent cinsinden
                ],
                'quantity' => $item->quantity,
            ];
        }

        return $lineItems;
    }

    /**
     * Stripe webhook signature doğrulama
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('stripe.webhook_secret')
            );

            return $event;
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook Invalid payload: ' . $e->getMessage());
            return false;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe Webhook Invalid signature: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Checkout Session bilgilerini alır
     */
    public function retrieveSession($sessionId)
    {
        try {
            return Session::retrieve($sessionId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Session Retrieve Error: ' . $e->getMessage());
            throw new \Exception('Stripe oturum bilgisi alınamadı');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function initiatePayment(Order $order, array $customerData): array
    {
        try {
            $result = $this->createCheckoutSession($order);
            
            return [
                'success' => true,
                'data' => [
                    'session_id' => $result['session_id'],
                    'checkout_url' => $result['checkout_url'],
                ],
                'error' => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleCallback(array $request): array
    {
        $sessionId = $request['session_id'] ?? null;
        
        if (!$sessionId) {
            return [
                'success' => false,
                'order_id' => null,
                'transaction_id' => null,
                'error' => 'Session ID not provided',
            ];
        }

        try {
            $session = $this->retrieveSession($sessionId);
            
            return [
                'success' => $session->payment_status === 'paid',
                'order_id' => (int) ($session->metadata->order_id ?? $session->client_reference_id),
                'transaction_id' => $session->payment_intent,
                'error' => $session->payment_status !== 'paid' ? 'Payment not completed' : null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'order_id' => null,
                'transaction_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleWebhook(array $request): array
    {
        $event = $request['event'] ?? null;
        
        if (!$event) {
            return [
                'success' => false,
                'order_id' => null,
                'status' => 'error',
                'error' => 'Event not provided',
            ];
        }

        $orderId = $event->data->object->metadata->order_id ?? null;
        
        return [
            'success' => true,
            'order_id' => (int) $orderId,
            'status' => $event->type,
            'error' => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function refund(string $transactionId, float $amount, ?string $reason = null): array
    {
        try {
            $refund = \Stripe\Refund::create([
                'payment_intent' => $transactionId,
                'amount' => intval($amount * 100), // Kuruş cinsinden
                'reason' => $reason === 'duplicate' ? 'duplicate' : ($reason === 'fraudulent' ? 'fraudulent' : 'requested_by_customer'),
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'error' => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'refund_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isTestMode(): bool
    {
        return str_starts_with($this->secretKey, 'sk_test_');
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'stripe';
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigured(): bool
    {
        return !empty($this->secretKey);
    }
}
