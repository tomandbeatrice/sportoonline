<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeliverService implements ShippingProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('shipping.providers.geliver.api_key');
        $this->baseUrl = config('shipping.providers.geliver.base_url');
    }

    public function getQuote(array $shipmentData): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->post("{$this->baseUrl}/quote", [
                    'origin' => $shipmentData['origin'],
                    'destination' => $shipmentData['destination'],
                    'weight' => $shipmentData['weight'],
                    'dimensions' => $shipmentData['dimensions'] ?? null,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'geliver',
                    'price' => $data['price'] ?? 0,
                    'currency' => $data['currency'] ?? 'TRY',
                    'estimated_delivery_days' => $data['estimated_delivery_days'] ?? 3,
                ];
            }

            return [
                'success' => false,
                'provider' => 'geliver',
                'error' => $response->json()['message'] ?? 'Fiyat teklifi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Geliver fiyat teklifi hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'geliver',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createShipment(array $shipmentData): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->post("{$this->baseUrl}/shipments", [
                    'order_id' => $shipmentData['order_id'],
                    'recipient' => $shipmentData['recipient'],
                    'sender' => $shipmentData['sender'],
                    'items' => $shipmentData['items'],
                    'weight' => $shipmentData['weight'],
                    'dimensions' => $shipmentData['dimensions'] ?? null,
                    'payment_method' => $shipmentData['payment_method'] ?? 'prepaid',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'geliver',
                    'tracking_code' => $data['tracking_code'] ?? null,
                    'shipment_id' => $data['id'] ?? null,
                ];
            }

            return [
                'success' => false,
                'provider' => 'geliver',
                'error' => $response->json()['message'] ?? 'Gönderi oluşturulamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Geliver gönderi oluşturma hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'geliver',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function trackShipment(string $trackingCode): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->get("{$this->baseUrl}/shipments/{$trackingCode}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'geliver',
                    'tracking_code' => $trackingCode,
                    'status' => $data['shipment_status'] ?? 'Durum belirtilmedi',
                    'updated_at' => $data['updated_at'] ?? null,
                    'checkpoints' => $data['checkpoints'] ?? [],
                ];
            }

            return [
                'success' => false,
                'provider' => 'geliver',
                'error' => $response->json()['message'] ?? 'Takip bilgisi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Geliver takip hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'geliver',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelShipment(string $trackingCode): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->delete("{$this->baseUrl}/shipments/{$trackingCode}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider' => 'geliver',
                    'message' => 'Gönderi iptal edildi',
                ];
            }

            return [
                'success' => false,
                'provider' => 'geliver',
                'error' => $response->json()['message'] ?? 'Gönderi iptal edilemedi',
            ];
        } catch (\Throwable $e) {
            Log::error('Geliver iptal hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'geliver',
                'error' => $e->getMessage(),
            ];
        }
    }
}
