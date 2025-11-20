<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\AbstractPaymentGateway;

class ZiraatPayGateway extends AbstractPaymentGateway
{
    /**
     * Validate gateway configuration
     */
    public function isConfigured(): bool
    {
        return !empty($this->getCredential('client_id')) &&
               !empty($this->getCredential('store_key'));
    }

    /**
     * Initialize payment and get payment form/redirect URL
     */
    public function initiatePayment(Order $order, array $customerData): array
    {
        try {
            if (!$this->isConfigured()) {
                return $this->errorResponse('ZiraatPay gateway is not configured properly');
            }

            $clientId = $this->getCredential('client_id');
            $storeKey = $this->getCredential('store_key');
            $conversationId = $this->generateConversationId($order->id);

            // Calculate hash
            $hashData = $clientId . $order->id . $this->formatAmount($order->total) . 
                       route('payment.callback.ziraatpay') . 
                       route('payment.fail.ziraatpay') . 
                       ($this->isTestMode() ? 'TEST' : 'PROD') . 
                       $storeKey;
            $hash = hash('sha512', $hashData);

            // Prepare payment data
            $paymentData = [
                'clientid' => $clientId,
                'amount' => $this->formatAmount($order->total),
                'oid' => $order->id,
                'okUrl' => route('payment.callback.ziraatpay'),
                'failUrl' => route('payment.fail.ziraatpay'),
                'islemtipi' => 'Auth',
                'taksit' => '',
                'rnd' => $conversationId,
                'hash' => $hash,
                'currency' => '949', // TRY
                'storetype' => '3d_pay',
                'lang' => 'tr',
                'refreshtime' => '5',
                'encoding' => 'UTF-8',
                'BillToName' => $customerData['first_name'] ?? $order->user->name,
                'BillToCompany' => '',
                'email' => $customerData['email'] ?? $order->user->email,
                'tel' => $customerData['phone'] ?? '',
                'BillToStreet1' => $order->billing_address ?? $order->shipping_address,
                'BillToCity' => $customerData['city'] ?? 'Istanbul',
                'BillToStateProv' => $customerData['state'] ?? '',
                'BillToPostalCode' => $customerData['zip_code'] ?? '34000',
                'BillToCountry' => 'TR',
            ];

            // Generate HTML form
            $apiUrl = $this->isTestMode() 
                ? 'https://vpostest.ziraatbank.com.tr/PaymentGateway/Payment/PaymentAuth'
                : 'https://vpos.ziraatbank.com.tr/PaymentGateway/Payment/PaymentAuth';

            $html = '<html><body><form id="paymentForm" method="POST" action="' . $apiUrl . '">';
            foreach ($paymentData as $key => $value) {
                $html .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
            }
            $html .= '</form><script>document.getElementById("paymentForm").submit();</script></body></html>';

            $this->logActivity('initiate_payment', [
                'order_id' => $order->id,
                'conversation_id' => $conversationId,
            ]);

            return $this->successResponse([
                'data' => [
                    'payment_form_html' => $html,
                    'payment_page_url' => $apiUrl,
                    'conversation_id' => $conversationId,
                    'post_data' => $paymentData,
                ],
            ]);

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
            $orderId = $request['oid'] ?? null;
            $mdStatus = $request['mdStatus'] ?? null;
            $response = $request['Response'] ?? null;
            $hash = $request['HASH'] ?? null;

            if (!$orderId || !$mdStatus || !$hash) {
                return $this->errorResponse('Invalid callback data');
            }

            // Verify hash
            $storeKey = $this->getCredential('store_key');
            $hashParams = $request['clientid'] . $orderId . $request['AuthCode'] . 
                         $request['ProcReturnCode'] . $response . $mdStatus . $storeKey;
            $calculatedHash = hash('sha512', $hashParams);

            if (strtoupper($hash) !== strtoupper($calculatedHash)) {
                $this->logActivity('handle_callback_error', [
                    'error' => 'Hash verification failed',
                    'order_id' => $orderId,
                ], 'error');

                return $this->errorResponse('Hash verification failed');
            }

            $this->logActivity('handle_callback', [
                'order_id' => $orderId,
                'md_status' => $mdStatus,
                'response' => $response,
            ]);

            // mdStatus 1-2-3-4 = successful 3D auth
            if (in_array($mdStatus, ['1', '2', '3', '4']) && $response === 'Approved') {
                return $this->successResponse([
                    'order_id' => (int) $orderId,
                    'transaction_id' => $request['TransId'] ?? $orderId,
                    'data' => [
                        'auth_code' => $request['AuthCode'] ?? '',
                        'amount' => $request['amount'] ?? 0,
                        'currency' => 'TRY',
                    ],
                ]);
            }

            return $this->errorResponse(
                $request['ErrMsg'] ?? 'Payment failed',
                [
                    'error_code' => $request['ProcReturnCode'] ?? null,
                    'order_id' => (int) $orderId,
                ]
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
                return $this->errorResponse('ZiraatPay gateway is not configured properly');
            }

            $clientId = $this->getCredential('client_id');
            $storeKey = $this->getCredential('store_key');
            $username = $this->getCredential('api_username');
            $password = $this->getCredential('api_password');

            // Calculate hash for refund
            $hashData = $clientId . $transactionId . $this->formatAmount($amount) . $storeKey;
            $hash = hash('sha512', $hashData);

            // Prepare refund data
            $refundData = [
                'clientid' => $clientId,
                'oid' => $transactionId,
                'amount' => $this->formatAmount($amount),
                'currency' => '949',
                'hash' => $hash,
                'username' => $username,
                'password' => $password,
            ];

            // Make API request
            $url = $this->isTestMode() 
                ? 'https://vpostest.ziraatbank.com.tr/PaymentGateway/Payment/Refund'
                : 'https://vpos.ziraatbank.com.tr/PaymentGateway/Payment/Refund';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($refundData));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !$this->isTestMode());
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result, true);

            $this->logActivity('refund', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'response' => $result['Response'] ?? null,
            ]);

            if (isset($result['Response']) && $result['Response'] === 'Approved') {
                return $this->successResponse([
                    'refund_id' => $result['TransId'] ?? $transactionId,
                ]);
            }

            return $this->errorResponse(
                $result['ErrMsg'] ?? 'Refund failed',
                ['error_code' => $result['ProcReturnCode'] ?? null]
            );

        } catch (\Exception $e) {
            $this->logActivity('refund_error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ], 'error');

            return $this->errorResponse($e->getMessage());
        }
    }
}
