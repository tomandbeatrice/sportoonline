<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Log;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected PaymentGateway $config;
    protected bool $testMode;
    protected array $credentials;

    public function __construct(PaymentGateway $config)
    {
        $this->config = $config;
        $this->testMode = $config->is_test_mode;
        $this->credentials = $config->credentials ?? [];
    }

    /**
     * Get gateway name
     */
    public function getName(): string
    {
        return $this->config->name;
    }

    /**
     * Check if gateway is in test mode
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    /**
     * Log payment activity
     */
    protected function logActivity(string $action, array $data, ?string $level = 'info'): void
    {
        Log::channel('payment')->{$level}("[{$this->getName()}] {$action}", [
            'gateway' => $this->getName(),
            'test_mode' => $this->testMode,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get credential value
     */
    protected function getCredential(string $key, $default = null)
    {
        return $this->credentials[$key] ?? $default;
    }

    /**
     * Build error response
     */
    protected function errorResponse(string $message, ?array $additionalData = []): array
    {
        return array_merge([
            'success' => false,
            'error' => $message,
        ], $additionalData);
    }

    /**
     * Build success response
     */
    protected function successResponse(array $data): array
    {
        return array_merge([
            'success' => true,
            'error' => null,
        ], $data);
    }

    /**
     * Format amount for gateway (convert to smallest currency unit)
     */
    protected function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * Generate unique conversation ID
     */
    protected function generateConversationId(int $orderId): string
    {
        return 'ORD-' . $orderId . '-' . time() . '-' . substr(md5(uniqid()), 0, 8);
    }
}
