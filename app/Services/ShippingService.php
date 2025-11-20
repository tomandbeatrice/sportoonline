<?php

namespace App\Services;

use App\Models\Order;
use App\Services\Shipping\GeliverService;
use App\Services\Shipping\ArasKargoService;
use App\Services\Shipping\SuratKargoService;
use App\Services\Shipping\PTTKargoService;
use App\Services\Shipping\YurticiKargoService;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    protected array $providers = [];

    public function __construct()
    {
        $this->initializeProviders();
    }

    private function initializeProviders(): void
    {
        $this->providers = [
            'geliver' => new GeliverService(),
            'aras' => new ArasKargoService(),
            'surat' => new SuratKargoService(),
            'ptt' => new PTTKargoService(),
            'yurtici' => new YurticiKargoService(),
        ];
    }

    public function getAvailableProviders(): array
    {
        $providers = [];
        
        foreach (config('shipping.providers') as $key => $config) {
            if ($config['enabled'] ?? false) {
                $providers[] = [
                    'code' => $key,
                    'name' => $config['name'],
                    'logo' => $config['logo'],
                ];
            }
        }

        return $providers;
    }

    public function getQuotes(array $shipmentData): array
    {
        $quotes = [];

        foreach ($this->providers as $providerKey => $provider) {
            if (!config("shipping.providers.{$providerKey}.enabled", false)) {
                continue;
            }

            $result = $provider->getQuote($shipmentData);
            
            if ($result['success']) {
                $quotes[] = array_merge($result, [
                    'provider_name' => config("shipping.providers.{$providerKey}.name"),
                    'provider_logo' => config("shipping.providers.{$providerKey}.logo"),
                ]);
            }
        }

        // Fiyata göre sırala
        usort($quotes, fn($a, $b) => $a['price'] <=> $b['price']);

        return $quotes;
    }

    public function createShipment(Order $order, string $provider = null): array
    {
        try {
            $providerKey = $provider ?? config('shipping.default');
            
            if (!isset($this->providers[$providerKey])) {
                return [
                    'success' => false,
                    'error' => 'Geçersiz kargo sağlayıcı',
                ];
            }

            $shipmentData = $this->prepareShipmentData($order);
            $result = $this->providers[$providerKey]->createShipment($shipmentData);

            if ($result['success']) {
                // Siparişi güncelle
                $order->update([
                    'shipping_provider' => $providerKey,
                    'tracking_code' => $result['tracking_code'],
                    'shipping_status' => 'shipped',
                ]);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('Kargo servisi hatası', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function trackShipment(string $trackingCode, string $provider = null): array
    {
        try {
            if ($provider && isset($this->providers[$provider])) {
                return $this->providers[$provider]->trackShipment($trackingCode);
            }

            // Provider belirtilmemişse tüm sağlayıcılarda ara
            foreach ($this->providers as $providerService) {
                $result = $providerService->trackShipment($trackingCode);
                
                if ($result['success']) {
                    return $result;
                }
            }

            return [
                'success' => false,
                'error' => 'Takip numarası bulunamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Kargo takip hatası', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelShipment(string $trackingCode, string $provider): array
    {
        try {
            if (!isset($this->providers[$provider])) {
                return [
                    'success' => false,
                    'error' => 'Geçersiz kargo sağlayıcı',
                ];
            }

            return $this->providers[$provider]->cancelShipment($trackingCode);
        } catch (\Throwable $e) {
            Log::error('Kargo iptal hatası', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function prepareShipmentData(Order $order): array
    {
        return [
            'order_id' => $order->id,
            'recipient' => [
                'name' => $order->shipping_name ?? $order->name,
                'address' => $order->shipping_address ?? $order->address,
                'phone' => $order->phone,
                'city' => $order->shipping_city ?? 'İstanbul',
                'district' => $order->shipping_district ?? 'Kadıköy',
            ],
            'sender' => [
                'name' => $order->vendor->name ?? config('app.name'),
                'address' => $order->vendor->address ?? 'Varsayılan Adres',
                'phone' => $order->vendor->phone ?? '05001234567',
                'city' => $order->vendor->city ?? 'İstanbul',
                'district' => $order->vendor->district ?? 'Şişli',
            ],
            'items' => $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            })->toArray(),
            'weight' => $order->items->sum('quantity') * 0.5, // Varsayılan ağırlık
            'dimensions' => null,
            'payment_method' => $order->payment_method,
            'invoice_amount' => $order->total_amount,
        ];
    }
}