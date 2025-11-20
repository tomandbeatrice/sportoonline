<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGateway;
use App\Services\Payment\Gateways\IyzicoGateway;
use App\Services\Payment\Gateways\PayTRGateway;
use App\Services\Payment\Gateways\MokaPosGateway;
use App\Services\Payment\Gateways\ZiraatPayGateway;
use Illuminate\Support\Facades\Cache;

class PaymentManager
{
    private const CACHE_KEY = 'active_payment_gateways';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get active payment gateway by name
     */
    public function getGateway(string $name): ?PaymentGatewayInterface
    {
        $config = PaymentGateway::where('name', $name)
            ->where('is_active', true)
            ->first();

        if (!$config) {
            return null;
        }

        return $this->createGatewayInstance($config);
    }

    /**
     * Get all active payment gateways
     */
    public function getActiveGateways(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $configs = PaymentGateway::active()->ordered()->get();
            
            $gateways = [];
            foreach ($configs as $config) {
                $gateway = $this->createGatewayInstance($config);
                if ($gateway && $gateway->isConfigured()) {
                    $gateways[] = [
                        'name' => $config->name,
                        'display_name' => $config->display_name,
                        'description' => $config->description,
                        'is_test_mode' => $config->is_test_mode,
                        'min_amount' => $config->min_amount,
                        'max_amount' => $config->max_amount,
                        'instance' => $gateway,
                    ];
                }
            }
            
            return $gateways;
        });
    }

    /**
     * Get default active gateway
     */
    public function getDefaultGateway(): ?PaymentGatewayInterface
    {
        $config = PaymentGateway::active()
            ->ordered()
            ->first();

        if (!$config) {
            return null;
        }

        return $this->createGatewayInstance($config);
    }

    /**
     * Get available gateway names for admin dropdown
     */
    public function getAvailableGatewayNames(): array
    {
        return [
            'iyzico' => [
                'display_name' => 'Iyzico',
                'provider' => IyzicoGateway::class,
                'description' => 'Türkiye\'nin önde gelen ödeme gateway\'i',
                'required_credentials' => ['api_key', 'secret_key'],
            ],
            'paytr' => [
                'display_name' => 'PayTR',
                'provider' => PayTRGateway::class,
                'description' => 'Yerli ödeme çözümü',
                'required_credentials' => ['merchant_id', 'merchant_key', 'merchant_salt'],
            ],
            'mokapos' => [
                'display_name' => 'MokaPOS',
                'provider' => MokaPosGateway::class,
                'description' => 'Akbank sanal POS çözümü',
                'required_credentials' => ['dealer_code', 'username', 'password', 'checkout_key'],
            ],
            'ziraatpay' => [
                'display_name' => 'ZiraatPay',
                'provider' => ZiraatPayGateway::class,
                'description' => 'Ziraat Bankası sanal POS',
                'required_credentials' => ['client_id', 'store_key', 'api_username', 'api_password'],
            ],
        ];
    }

    /**
     * Clear cached gateways
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Validate gateway configuration
     */
    public function validateGatewayConfig(string $gatewayName, array $credentials): bool
    {
        $availableGateways = $this->getAvailableGatewayNames();
        
        if (!isset($availableGateways[$gatewayName])) {
            return false;
        }

        $requiredKeys = $availableGateways[$gatewayName]['required_credentials'];
        
        foreach ($requiredKeys as $key) {
            if (empty($credentials[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create gateway instance from config
     */
    private function createGatewayInstance(PaymentGateway $config): ?PaymentGatewayInterface
    {
        $provider = $config->provider;

        if (!class_exists($provider)) {
            return null;
        }

        try {
            return new $provider($config);
        } catch (\Exception $e) {
            \Log::error('Failed to create payment gateway instance', [
                'gateway' => $config->name,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get gateway statistics
     */
    public function getGatewayStats(string $gatewayName): array
    {
        $gateway = PaymentGateway::where('name', $gatewayName)->first();
        
        if (!$gateway) {
            return [];
        }

        $transactions = $gateway->transactions();

        return [
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $transactions->where('status', 'success')->count(),
            'failed_transactions' => $transactions->where('status', 'failed')->count(),
            'total_amount' => $transactions->where('status', 'success')->sum('amount'),
            'refunded_amount' => $transactions->where('status', 'refunded')->sum('refund_amount'),
            'average_amount' => $transactions->where('status', 'success')->avg('amount'),
        ];
    }
}
