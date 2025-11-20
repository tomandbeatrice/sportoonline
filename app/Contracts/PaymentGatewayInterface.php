<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Initialize payment and get payment form/redirect URL
     *
     * @param Order $order
     * @param array $customerData
     * @return array ['success' => bool, 'data' => mixed, 'error' => string|null]
     */
    public function initiatePayment(Order $order, array $customerData): array;

    /**
     * Handle payment callback from gateway
     *
     * @param array $request
     * @return array ['success' => bool, 'order_id' => int, 'transaction_id' => string, 'error' => string|null]
     */
    public function handleCallback(array $request): array;

    /**
     * Handle webhook notification from gateway
     *
     * @param array $request
     * @return array ['success' => bool, 'order_id' => int, 'status' => string, 'error' => string|null]
     */
    public function handleWebhook(array $request): array;

    /**
     * Refund payment
     *
     * @param string $transactionId
     * @param float $amount
     * @param string|null $reason
     * @return array ['success' => bool, 'refund_id' => string|null, 'error' => string|null]
     */
    public function refund(string $transactionId, float $amount, ?string $reason = null): array;

    /**
     * Check if gateway is in test mode
     *
     * @return bool
     */
    public function isTestMode(): bool;

    /**
     * Get gateway name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Validate gateway configuration
     *
     * @return bool
     */
    public function isConfigured(): bool;
}
