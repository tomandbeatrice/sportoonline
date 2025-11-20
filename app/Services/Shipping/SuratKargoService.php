<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuratKargoService implements ShippingProviderInterface
{
    protected string $username;
    protected string $password;
    protected string $customerCode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->username = config('shipping.providers.surat.username');
        $this->password = config('shipping.providers.surat.password');
        $this->customerCode = config('shipping.providers.surat.customer_code');
        $this->baseUrl = config('shipping.providers.surat.base_url');
    }

    public function getQuote(array $shipmentData): array
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->post("{$this->baseUrl}/pricing/calculate", [
                    'customer_code' => $this->customerCode,
                    'sender_city' => $shipmentData['origin']['city'],
                    'receiver_city' => $shipmentData['destination']['city'],
                    'receiver_district' => $shipmentData['destination']['district'],
                    'weight' => $shipmentData['weight'],
                    'desi' => $this->calculateDesi($shipmentData['dimensions'] ?? []),
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'surat',
                    'price' => $data['price'] ?? 0,
                    'currency' => 'TRY',
                    'estimated_delivery_days' => $data['delivery_time'] ?? 3,
                ];
            }

            return [
                'success' => false,
                'provider' => 'surat',
                'error' => 'Fiyat teklifi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Sürat Kargo fiyat teklifi hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'surat',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createShipment(array $shipmentData): array
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->post("{$this->baseUrl}/shipments/create", [
                    'customer_code' => $this->customerCode,
                    'receiver' => [
                        'name' => $shipmentData['recipient']['name'],
                        'address' => $shipmentData['recipient']['address'],
                        'phone' => $shipmentData['recipient']['phone'],
                        'city' => $shipmentData['recipient']['city'],
                        'district' => $shipmentData['recipient']['district'],
                    ],
                    'sender' => [
                        'name' => $shipmentData['sender']['name'],
                        'address' => $shipmentData['sender']['address'],
                        'phone' => $shipmentData['sender']['phone'],
                        'city' => $shipmentData['sender']['city'],
                        'district' => $shipmentData['sender']['district'],
                    ],
                    'weight' => $shipmentData['weight'],
                    'piece_count' => count($shipmentData['items']),
                    'payment_type' => 'SENDER', // Gönderen öder
                    'description' => 'E-Ticaret Gönderisi',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'surat',
                    'tracking_code' => $data['barcode'] ?? null,
                    'shipment_id' => $data['shipment_id'] ?? null,
                ];
            }

            return [
                'success' => false,
                'provider' => 'surat',
                'error' => 'Gönderi oluşturulamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Sürat Kargo gönderi oluşturma hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'surat',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function trackShipment(string $trackingCode): array
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->get("{$this->baseUrl}/shipments/track/{$trackingCode}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'surat',
                    'tracking_code' => $trackingCode,
                    'status' => $data['status_description'] ?? 'Durum belirtilmedi',
                    'updated_at' => $data['last_update'] ?? null,
                    'checkpoints' => $data['movements'] ?? [],
                ];
            }

            return [
                'success' => false,
                'provider' => 'surat',
                'error' => 'Takip bilgisi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Sürat Kargo takip hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'surat',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelShipment(string $trackingCode): array
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->delete("{$this->baseUrl}/shipments/{$trackingCode}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider' => 'surat',
                    'message' => 'Gönderi iptal edildi',
                ];
            }

            return [
                'success' => false,
                'provider' => 'surat',
                'error' => 'Gönderi iptal edilemedi',
            ];
        } catch (\Throwable $e) {
            Log::error('Sürat Kargo iptal hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'surat',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function calculateDesi(array $dimensions): float
    {
        if (empty($dimensions)) {
            return 0;
        }

        $length = $dimensions['length'] ?? 0;
        $width = $dimensions['width'] ?? 0;
        $height = $dimensions['height'] ?? 0;

        return ($length * $width * $height) / 3000;
    }
}
