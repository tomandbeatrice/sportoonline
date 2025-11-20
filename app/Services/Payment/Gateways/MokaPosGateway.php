<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\AbstractPaymentGateway;

class MokaPosGateway extends AbstractPaymentGateway
{
    /**
     * Validate gateway configuration
     */
    public function isConfigured(): bool
    {
        return !empty($this->getCredential('dealer_code')) &&
               !empty($this->getCredential('username')) &&
               !empty($this->getCredential('password'));
    }

    /**
     * Initialize payment and get payment form/redirect URL
     */
    public function initiatePayment(Order $order, array $customerData): array
    {
        try {
            if (!$this->isConfigured()) {
                return $this->errorResponse('MokaPOS gateway is not configured properly');
            }

            $dealerCode = $this->getCredential('dealer_code');
            $username = $this->getCredential('username');
            $password = $this->getCredential('password');
            $checkoutKey = $this->getCredential('checkout_key');

            // Generate hash
            $hashText = $dealerCode . 'MK' . $username . 'PD' . $password;
            $hash = hash('sha256', $hashText);

            // Prepare payment data
            $paymentData = [
                'DealerCode' => $dealerCode,
                'Username' => $username,
                'Password' => $password,
                'CheckKey' => $hash,
                'OrderId' => $order->id,
                'Amount' => $this->formatAmount($order->total),
                'Currency' => '1', // 1 = TRY
                'InstallmentCount' => 0,
                'OtherTrxCode' => $this->generateConversationId($order->id),
                'SubMerchantId' => '',
                'RedirectUrl' => route('payment.callback.mokapos'),
                'RedirectType' => '0', // 0 = POST
                'IsPoolPayment' => 0,
                'IsTokenizedPayment' => 0,
                'BuyerInformation' => [
                    'BuyerFullName' => $customerData['first_name'] ?? $order->user->name,
                    'BuyerEmail' => $customerData['email'] ?? $order->user->email,
                    'BuyerGsmNumber' => $customerData['phone'] ?? '',
                    'BuyerAddress' => $order->shipping_address,
                ],
                'BasketInformation' => [
                    'BasketId' => $order->id,
                    'BasketItems' => $this->prepareBasket($order),
                ],
            ];

            // Make API request
            $url = $this->isTestMode() 
                ? 'https://service.testmoka.com/PaymentDealer/DoDirectPaymentThreeD'
                : 'https://service.moka.com/PaymentDealer/DoDirectPaymentThreeD';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !$this->isTestMode());
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $result = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new \Exception(curl_error($ch));
            }
            
            curl_close($ch);

            $result = json_decode($result, true);

            $this->logActivity('initiate_payment', [
                'order_id' => $order->id,
                'result_code' => $result['ResultCode'] ?? null,
            ]);

            if (isset($result['ResultCode']) && $result['ResultCode'] === 'Success') {
                return $this->successResponse([
                    'data' => [
                        'payment_page_url' => $result['Data'],
                        'conversation_id' => $paymentData['OtherTrxCode'],
                    ],
                ]);
            }

            return $this->errorResponse(
                $result['ResultMessage'] ?? 'Payment initialization failed',
                ['error_code' => $result['ResultCode'] ?? null]
            );

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
            $resultCode = $request['ResultCode'] ?? null;
            $orderId = $request['OrderId'] ?? null;
            $trxCode = $request['TrxCode'] ?? null;

            if (!$resultCode || !$orderId) {
                return $this->errorResponse('Invalid callback data');
            }

            $this->logActivity('handle_callback', [
                'order_id' => $orderId,
                'result_code' => $resultCode,
            ]);

            if ($resultCode === 'Success') {
                return $this->successResponse([
                    'order_id' => (int) $orderId,
                    'transaction_id' => $trxCode ?? $orderId,
                    'data' => [
                        'amount' => $request['Amount'] ?? 0,
                        'currency' => 'TRY',
                    ],
                ]);
            }

            return $this->errorResponse(
                $request['ResultMessage'] ?? 'Payment failed',
                [
                    'error_code' => $resultCode,
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
                return $this->errorResponse('MokaPOS gateway is not configured properly');
            }

            $dealerCode = $this->getCredential('dealer_code');
            $username = $this->getCredential('username');
            $password = $this->getCredential('password');

            // Generate hash
            $hashText = $dealerCode . 'MK' . $username . 'PD' . $password;
            $hash = hash('sha256', $hashText);

            // Prepare refund data
            $refundData = [
                'DealerCode' => $dealerCode,
                'Username' => $username,
                'Password' => $password,
                'CheckKey' => $hash,
                'TrxCode' => $transactionId,
                'Amount' => $this->formatAmount($amount),
                'OtherTrxCode' => 'REFUND-' . time(),
            ];

            // Make API request
            $url = $this->isTestMode() 
                ? 'https://service.testmoka.com/PaymentDealer/DoCreateRefundRequest'
                : 'https://service.moka.com/PaymentDealer/DoCreateRefundRequest';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($refundData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !$this->isTestMode());
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result, true);

            $this->logActivity('refund', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'result_code' => $result['ResultCode'] ?? null,
            ]);

            if (isset($result['ResultCode']) && $result['ResultCode'] === 'Success') {
                return $this->successResponse([
                    'refund_id' => $result['Data'] ?? $transactionId,
                ]);
            }

            return $this->errorResponse(
                $result['ResultMessage'] ?? 'Refund failed',
                ['error_code' => $result['ResultCode'] ?? null]
            );

        } catch (\Exception $e) {
            $this->logActivity('refund_error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ], 'error');

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Prepare basket items for MokaPOS
     */
    private function prepareBasket(Order $order): array
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'ProductId' => $item->product_id,
                'ProductName' => $item->product_name,
                'Quantity' => $item->quantity,
                'Price' => $this->formatAmount($item->unit_price),
                'Amount' => $this->formatAmount($item->total_price),
            ];
        }
        return $items;
    }
}
