<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\AbstractPaymentGateway;

class PayTRGateway extends AbstractPaymentGateway
{
    /**
     * Validate gateway configuration
     */
    public function isConfigured(): bool
    {
        return !empty($this->getCredential('merchant_id')) &&
               !empty($this->getCredential('merchant_key')) &&
               !empty($this->getCredential('merchant_salt'));
    }

    /**
     * Initialize payment and get payment form/redirect URL
     */
    public function initiatePayment(Order $order, array $customerData): array
    {
        try {
            if (!$this->isConfigured()) {
                return $this->errorResponse('PayTR gateway is not configured properly');
            }

            $merchantId = $this->getCredential('merchant_id');
            $merchantKey = $this->getCredential('merchant_key');
            $merchantSalt = $this->getCredential('merchant_salt');
            $merchantOkUrl = route('payment.callback.paytr');
            $merchantFailUrl = route('payment.fail.paytr');

            // Prepare basket
            $userBasket = base64_encode(json_encode($this->prepareBasket($order)));

            // Calculate hash
            $hashStr = $merchantId . request()->ip() . $order->id . 
                      $this->formatAmount($order->total) . 'TL' . 
                      ($this->isTestMode() ? '1' : '0') . $merchantSalt;
            $paytrToken = base64_encode(hash_hmac('sha256', $hashStr . $merchantSalt, $merchantKey, true));

            // Prepare POST data
            $postData = [
                'merchant_id' => $merchantId,
                'user_ip' => request()->ip(),
                'merchant_oid' => $order->id,
                'email' => $customerData['email'] ?? $order->user->email,
                'payment_amount' => (int) ($order->total * 100), // Convert to kuruş
                'paytr_token' => $paytrToken,
                'user_basket' => $userBasket,
                'debug_on' => $this->isTestMode() ? 1 : 0,
                'no_installment' => 0,
                'max_installment' => 0,
                'user_name' => $customerData['first_name'] ?? $order->user->name,
                'user_address' => $order->shipping_address,
                'user_phone' => $customerData['phone'] ?? '',
                'merchant_ok_url' => $merchantOkUrl,
                'merchant_fail_url' => $merchantFailUrl,
                'timeout_limit' => 30,
                'currency' => 'TL',
                'test_mode' => $this->isTestMode() ? 1 : 0,
            ];

            // Make API request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/api/get-token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

            $result = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new \Exception(curl_error($ch));
            }
            
            curl_close($ch);

            $result = json_decode($result, true);

            $this->logActivity('initiate_payment', [
                'order_id' => $order->id,
                'status' => $result['status'] ?? 'error',
            ]);

            if (isset($result['status']) && $result['status'] === 'success') {
                return $this->successResponse([
                    'data' => [
                        'payment_page_url' => 'https://www.paytr.com/odeme/guvenli/' . $result['token'],
                        'token' => $result['token'],
                        'conversation_id' => $order->id,
                    ],
                ]);
            }

            return $this->errorResponse($result['reason'] ?? 'Payment initialization failed');

        } catch (\Exception $e) {
            $this->logActivity('initiate_payment_error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ], 'error');

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Handle payment callback from gateway
     */
    public function handleCallback(array $request): array
    {
        try {
            $merchantOid = $request['merchant_oid'] ?? null;
            $status = $request['status'] ?? null;
            $totalAmount = $request['total_amount'] ?? null;
            $hash = $request['hash'] ?? null;

            if (!$merchantOid || !$status || !$totalAmount || !$hash) {
                return $this->errorResponse('Invalid callback data');
            }

            // Verify hash
            $merchantSalt = $this->getCredential('merchant_salt');
            $hashStr = $merchantOid . $merchantSalt . $status . $totalAmount;
            $calculatedHash = base64_encode(hash_hmac('sha256', $hashStr, $this->getCredential('merchant_key'), true));

            if ($hash !== $calculatedHash) {
                $this->logActivity('handle_callback_error', [
                    'error' => 'Hash verification failed',
                    'order_id' => $merchantOid,
                ], 'error');

                return $this->errorResponse('Hash verification failed');
            }

            $this->logActivity('handle_callback', [
                'order_id' => $merchantOid,
                'status' => $status,
            ]);

            if ($status === 'success') {
                return $this->successResponse([
                    'order_id' => (int) $merchantOid,
                    'transaction_id' => $request['payment_id'] ?? $merchantOid,
                    'data' => [
                        'paid_amount' => $totalAmount / 100,
                        'currency' => 'TRY',
                    ],
                ]);
            }

            return $this->errorResponse(
                $request['failed_reason_msg'] ?? 'Payment failed',
                ['order_id' => (int) $merchantOid]
            );

        } catch (\Exception $e) {
            $this->logActivity('handle_callback_error', [
                'error' => $e->getMessage(),
            ], 'error');

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Handle webhook notification from gateway
     */
    public function handleWebhook(array $request): array
    {
        return $this->handleCallback($request);
    }

    /**
     * Refund payment
     */
    public function refund(string $transactionId, float $amount, ?string $reason = null): array
    {
        try {
            if (!$this->isConfigured()) {
                return $this->errorResponse('PayTR gateway is not configured properly');
            }

            $merchantId = $this->getCredential('merchant_id');
            $merchantKey = $this->getCredential('merchant_key');
            $merchantSalt = $this->getCredential('merchant_salt');

            // Calculate hash
            $refundAmount = (int) ($amount * 100);
            $hashStr = $merchantId . $transactionId . $refundAmount . $merchantSalt;
            $token = base64_encode(hash_hmac('sha256', $hashStr, $merchantKey, true));

            // Prepare POST data
            $postData = [
                'merchant_id' => $merchantId,
                'merchant_oid' => $transactionId,
                'return_amount' => $refundAmount,
                'paytr_token' => $token,
                'reference_no' => $reason ?? 'Refund',
            ];

            // Make API request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/iade');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result, true);

            $this->logActivity('refund', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'status' => $result['status'] ?? 'error',
            ]);

            if (isset($result['status']) && $result['status'] === 'success') {
                return $this->successResponse([
                    'refund_id' => $result['refund_id'] ?? $transactionId,
                ]);
            }

            return $this->errorResponse($result['err_msg'] ?? 'Refund failed');

        } catch (\Exception $e) {
            $this->logActivity('refund_error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ], 'error');

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Prepare basket items for PayTR
     */
    private function prepareBasket(Order $order): array
    {
        $basket = [];
        foreach ($order->items as $item) {
            $basket[] = [
                $item->product_name,
                $this->formatAmount($item->unit_price),
                $item->quantity,
            ];
        }
        return $basket;
    }
}
