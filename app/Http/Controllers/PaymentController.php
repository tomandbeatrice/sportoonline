<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\IyzicoService;
use App\Services\PaytrService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Iyzipay\Model\PayWithIyzico;
use Iyzipay\Request\CreatePayWithIyzicoRequest;

class PaymentController extends Controller
{
    protected $iyzicoService;
    protected $paytrService;
    protected $stripeService;

    public function __construct(IyzicoService $iyzicoService, PaytrService $paytrService, StripeService $stripeService)
    {
        $this->iyzicoService = $iyzicoService;
        $this->paytrService = $paytrService;
        $this->stripeService = $stripeService;
    }

    /**
     * Initiates the payment process for a given order and returns the payment page URL.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:iyzico,paytr,stripe'
        ]);

        $order = Order::with('user', 'items.product.category')->findOrFail($request->order_id);

        // Ensure the user owns the order
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            switch ($request->payment_method) {
                case 'iyzico':
                    return $this->startIyzicoPayment($order);
                case 'paytr':
                    return $this->startPaytrPayment($order);
                case 'stripe':
                    return $this->startStripePayment($order);
                default:
                    return response()->json(['error' => 'Geçersiz ödeme yöntemi'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Payment initiation exception: ' . $e->getMessage());
            return response()->json(['error' => 'Ödeme işlemi sırasında bir hata oluştu.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Iyzico ödeme işlemini başlatır
     */
    protected function startIyzicoPayment(Order $order)
    {
        $paymentInitialize = $this->iyzicoService->initializePayment($order);

        if ($paymentInitialize->getStatus() === 'success') {
            return response()->json([
                'payment_page_url' => $paymentInitialize->getPayWithIyzicoPageUrl(),
            ]);
        } else {
            Log::error('Iyzico initialization failed: ' . $paymentInitialize->getErrorMessage());
            return response()->json([
                'error' => 'Ödeme başlatılamadı.',
                'message' => $paymentInitialize->getErrorMessage()
            ], 500);
        }
    }

    /**
     * PayTR ödeme işlemini başlatır
     */
    protected function startPaytrPayment(Order $order)
    {
        $paymentData = $this->paytrService->initializePayment($order);

        return response()->json([
            'payment_page_url' => $paymentData['iframe_url'],
            'token' => $paymentData['token']
        ]);
    }

    /**
     * Stripe ödeme işlemini başlatır
     */
    protected function startStripePayment(Order $order)
    {
        $sessionData = $this->stripeService->createCheckoutSession($order);

        return response()->json([
            'payment_page_url' => $sessionData['checkout_url'],
            'session_id' => $sessionData['session_id']
        ]);
    }

    /**
     * Handles the callback from Iyzico after a payment attempt.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentCallback(Request $request)
    {
        $token = $request->get('token');
        if (!$token) {
            return redirect(config('app.frontend_url') . '/payment/failure?reason=invalid_token');
        }

        // Retrieve payment details from Iyzico
        $iyzicoRequest = new CreatePayWithIyzicoRequest();
        $iyzicoRequest->setLocale(\Iyzipay\Model\Locale::TR);
        $iyzicoRequest->setConversationId("dummy-conversation-id"); // This can be anything for retrieval
        $iyzicoRequest->setToken($token);

        $paymentDetails = PayWithIyzico::create($iyzicoRequest, $this->iyzicoService->getOptions());

        $orderId = $paymentDetails->getBasketId();
        $order = Order::find($orderId);

        if (!$order) {
            Log::error("Iyzico callback: Order not found with ID: $orderId");
            return redirect(config('app.frontend_url') . '/payment/failure?reason=order_not_found');
        }

        if ($paymentDetails->getPaymentStatus() === 'SUCCESS') {
            // Payment is successful, update the order
            $order->status = 'paid';
            $order->transaction_id = $paymentDetails->getPaymentId(); // Store transaction ID
            $order->save();

            // Find or create payment transaction record
            $transaction = \App\Models\PaymentTransaction::firstOrCreate(
                ['transaction_id' => $paymentDetails->getPaymentId()],
                [
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'gateway' => 'iyzico',
                    'amount' => $order->total_amount,
                    'status' => 'completed',
                ]
            );

            // Send payment success email
            try {
                \Mail::to($order->email)->send(new \App\Mail\PaymentSuccess($order, $transaction));
            } catch (\Exception $e) {
                \Log::error('Payment success email failed: ' . $e->getMessage());
            }

            return redirect(config('app.frontend_url') . "/payment/success?order_id={$order->id}");
        } else {
            // Payment failed
            $order->status = 'failed';
            $order->save();

            // Send payment failed email
            try {
                $errorMessage = $paymentDetails->getErrorMessage() ?? 'Ödeme işlemi başarısız oldu.';
                \Mail::to($order->email)->send(new \App\Mail\PaymentFailed($order, $errorMessage));
            } catch (\Exception $e) {
                \Log::error('Payment failed email failed: ' . $e->getMessage());
            }

            Log::warning("Iyzico payment failed for Order ID: $orderId. Reason: " . $paymentDetails->getErrorMessage());
            return redirect(config('app.frontend_url') . '/payment/failure?reason=' . $paymentDetails->getErrorCode());
        }
    }

    /**
     * PayTR başarılı ödeme callback'i
     */
    public function paytrSuccess(Request $request)
    {
        return redirect(config('app.frontend_url') . '/payment/success?order_id=' . $request->get('order_id'));
    }

    /**
     * PayTR başarısız ödeme callback'i
     */
    public function paytrFailure(Request $request)
    {
        return redirect(config('app.frontend_url') . '/payment/failure?reason=payment_failed');
    }

    /**
     * PayTR IPN (Instant Payment Notification) callback'i
     * Bu metod PayTR'den POST ile çağrılır
     */
    public function paytrCallback(Request $request)
    {
        $post = $request->all();

        // Hash doğrulama
        if (!$this->paytrService->verifyCallback($post)) {
            Log::error('PayTR callback hash verification failed', $post);
            echo "PAYTR notification failed: bad hash";
            return;
        }

        // Sipariş ID'sini çıkar
        $orderId = $this->paytrService->extractOrderId($post['merchant_oid']);
        $order = Order::find($orderId);

        if (!$order) {
            Log::error("PayTR callback: Order not found with ID: $orderId");
            echo "PAYTR notification failed: order not found";
            return;
        }

        // Ödeme durumunu kontrol et
        if ($post['status'] === 'success') {
            $order->status = 'paid';
            $order->transaction_id = $post['merchant_oid'];
            $order->save();

            Log::info("PayTR payment successful for Order ID: $orderId");
            echo "OK";
        } else {
            $order->status = 'failed';
            $order->save();

            Log::warning("PayTR payment failed for Order ID: $orderId");
            echo "OK";
        }
    }

    /**
     * Stripe başarılı ödeme callback'i
     */
    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect(config('app.frontend_url') . '/payment/failure?reason=invalid_session');
        }

        try {
            $session = $this->stripeService->retrieveSession($sessionId);
            $orderId = $session->client_reference_id;
            $order = Order::find($orderId);

            if (!$order) {
                Log::error("Stripe success: Order not found with ID: $orderId");
                return redirect(config('app.frontend_url') . '/payment/failure?reason=order_not_found');
            }

            // Ödeme durumunu güncelle
            if ($session->payment_status === 'paid') {
                $order->status = 'paid';
                $order->save();

                Log::info("Stripe payment successful for Order ID: $orderId");
                return redirect(config('app.frontend_url') . "/payment/success?order_id={$orderId}");
            }

            return redirect(config('app.frontend_url') . '/payment/failure?reason=payment_not_completed');
        } catch (\Exception $e) {
            Log::error('Stripe success callback error: ' . $e->getMessage());
            return redirect(config('app.frontend_url') . '/payment/failure?reason=callback_error');
        }
    }

    /**
     * Stripe iptal ödeme callback'i
     */
    public function stripeCancel(Request $request)
    {
        return redirect(config('app.frontend_url') . '/payment/failure?reason=user_cancelled');
    }

    /**
     * Stripe webhook handler
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        $event = $this->stripeService->verifyWebhookSignature($payload, $signature);

        if (!$event) {
            Log::error('Stripe webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Webhook event tipine göre işlem yap
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $orderId = $session->client_reference_id;
                $order = Order::find($orderId);

                if ($order && $order->status !== 'paid') {
                    $order->status = 'paid';
                    $order->save();
                    Log::info("Stripe webhook: Order $orderId marked as paid");
                }
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata->order_id ?? null;

                if ($orderId) {
                    $order = Order::find($orderId);
                    if ($order) {
                        $order->status = 'failed';
                        $order->save();
                        Log::warning("Stripe webhook: Order $orderId payment failed");
                    }
                }
                break;

            default:
                Log::info('Stripe webhook: Unhandled event type ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }
}