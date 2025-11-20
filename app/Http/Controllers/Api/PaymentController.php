<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private PaymentManager $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * Get available payment gateways
     */
    public function gateways()
    {
        $gateways = $this->paymentManager->getActiveGateways();
        
        return response()->json([
            'success' => true,
            'gateways' => array_map(function ($gateway) {
                return [
                    'name' => $gateway['name'],
                    'display_name' => $gateway['display_name'],
                    'description' => $gateway['description'],
                    'is_test_mode' => $gateway['is_test_mode'],
                    'min_amount' => $gateway['min_amount'],
                    'max_amount' => $gateway['max_amount'],
                ];
            }, $gateways),
        ]);
    }

    /**
     * Initiate payment
     */
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'gateway' => 'required|string',
            'customer_data' => 'array',
        ]);

        try {
            $order = Order::with(['items.product', 'user'])->findOrFail($validated['order_id']);
            
            // Check order status
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'error' => 'Bu sipariş zaten ödenmiş',
                ], 400);
            }

            // Get gateway
            $gateway = $this->paymentManager->getGateway($validated['gateway']);
            
            if (!$gateway) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ödeme yöntemi bulunamadı veya aktif değil',
                ], 400);
            }

            // Create transaction record
            $transaction = PaymentTransaction::create([
                'order_id' => $order->id,
                'payment_gateway_id' => \App\Models\PaymentGateway::where('name', $validated['gateway'])->first()->id,
                'conversation_id' => 'CONV-' . $order->id . '-' . time(),
                'status' => 'pending',
                'amount' => $order->total,
                'currency' => 'TRY',
            ]);

            // Initiate payment
            $result = $gateway->initiatePayment($order, $validated['customer_data'] ?? []);

            if ($result['success']) {
                $transaction->update([
                    'status' => 'processing',
                    'request_data' => $result['data'],
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                ]);
            }

            $transaction->markAsFailed($result['error']);

            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 400);

        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'error' => $e->getMessage(),
                'order_id' => $validated['order_id'],
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Ödeme başlatılamadı',
            ], 500);
        }
    }

    /**
     * Handle payment callback - Iyzico
     */
    public function callbackIyzico(Request $request)
    {
        return $this->handleCallback('iyzico', $request->all());
    }

    /**
     * Handle payment callback - PayTR
     */
    public function callbackPaytr(Request $request)
    {
        return $this->handleCallback('paytr', $request->all());
    }

    /**
     * Handle payment callback - MokaPOS
     */
    public function callbackMokapos(Request $request)
    {
        return $this->handleCallback('mokapos', $request->all());
    }

    /**
     * Handle payment callback - ZiraatPay
     */
    public function callbackZiraatpay(Request $request)
    {
        return $this->handleCallback('ziraatpay', $request->all());
    }

    /**
     * Generic callback handler
     */
    private function handleCallback(string $gatewayName, array $data)
    {
        try {
            $gateway = $this->paymentManager->getGateway($gatewayName);
            
            if (!$gateway) {
                return response()->json(['success' => false, 'error' => 'Gateway not found'], 400);
            }

            $result = $gateway->handleCallback($data);

            if ($result['success']) {
                DB::transaction(function () use ($result) {
                    // Update order
                    $order = Order::findOrFail($result['order_id']);
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                    ]);

                    // Update transaction
                    $transaction = PaymentTransaction::where('order_id', $order->id)
                        ->where('status', 'processing')
                        ->latest()
                        ->first();

                    if ($transaction) {
                        $transaction->markAsSuccess($result['transaction_id'], $result['data'] ?? []);
                    }

                    // Fire payment received event
                    event(new \App\Events\PaymentReceived($order));
                });

                // Redirect to frontend success page
                return redirect(config('app.frontend_url', 'http://localhost:5173') . '/payment/success?order_id=' . $result['order_id']);
            }

            // Handle failure
            if (isset($result['order_id'])) {
                $transaction = PaymentTransaction::where('order_id', $result['order_id'])
                    ->where('status', 'processing')
                    ->latest()
                    ->first();

                if ($transaction) {
                    $transaction->markAsFailed($result['error']);
                }
            }

            // Redirect to frontend fail page
            return redirect(config('app.frontend_url', 'http://localhost:5173') . '/payment/failed?error=' . urlencode($result['error']));

        } catch (\Exception $e) {
            Log::error('Payment callback failed', [
                'gateway' => $gatewayName,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('payment.fail', ['error' => 'Ödeme işlemi başarısız']);
        }
    }

    /**
     * Refund payment
     */
    public function refund(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:payment_transactions,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string',
        ]);

        try {
            $transaction = PaymentTransaction::with('gateway')->findOrFail($validated['transaction_id']);

            if ($transaction->status !== 'success') {
                return response()->json([
                    'success' => false,
                    'error' => 'Sadece başarılı ödemeler iade edilebilir',
                ], 400);
            }

            if ($validated['amount'] > $transaction->amount) {
                return response()->json([
                    'success' => false,
                    'error' => 'İade tutarı ödeme tutarından fazla olamaz',
                ], 400);
            }

            $gateway = $this->paymentManager->getGateway($transaction->gateway->name);
            
            if (!$gateway) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ödeme yöntemi bulunamadı',
                ], 400);
            }

            $result = $gateway->refund(
                $transaction->transaction_id,
                $validated['amount'],
                $validated['reason'] ?? null
            );

            if ($result['success']) {
                $transaction->markAsRefunded($validated['amount'], $validated['reason'] ?? null);

                // Update order if full refund
                if ($validated['amount'] >= $transaction->amount) {
                    $transaction->order->update([
                        'payment_status' => 'refunded',
                        'status' => 'cancelled',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'refund_id' => $result['refund_id'],
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 400);

        } catch (\Exception $e) {
            Log::error('Refund failed', [
                'transaction_id' => $validated['transaction_id'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'İade işlemi başarısız',
            ], 500);
        }
    }
}
