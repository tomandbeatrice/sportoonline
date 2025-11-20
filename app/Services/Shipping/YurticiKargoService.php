<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YurticiKargoService implements ShippingProviderInterface
{
    protected string $username;
    protected string $password;
    protected string $customerCode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->username = config('shipping.providers.yurtici.username');
        $this->password = config('shipping.providers.yurtici.password');
        $this->customerCode = config('shipping.providers.yurtici.customer_code');
        $this->baseUrl = config('shipping.providers.yurtici.base_url');
    }

    public function getQuote(array $shipmentData): array
    {
        try {
            $token = $this->getAuthToken();
            
            if (!$token) {
                return [
                    'success' => false,
                    'provider' => 'yurtici',
                    'error' => 'Kimlik doğrulama başarısız',
                ];
            }

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/get-price", [
                    'senderCityId' => $shipmentData['origin']['city_id'] ?? null,
                    'receiverCityId' => $shipmentData['destination']['city_id'] ?? null,
                    'receiverDistrictId' => $shipmentData['destination']['district_id'] ?? null,
                    'kg' => $shipmentData['weight'],
                    'desi' => $this->calculateDesi($shipmentData['dimensions'] ?? []),
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'yurtici',
                    'price' => $data['price'] ?? 0,
                    'currency' => 'TRY',
                    'estimated_delivery_days' => 2,
                ];
            }

            return [
                'success' => false,
                'provider' => 'yurtici',
                'error' => 'Fiyat teklifi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Yurtiçi Kargo fiyat teklifi hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'yurtici',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createShipment(array $shipmentData): array
    {
        try {
            $token = $this->getAuthToken();
            
            if (!$token) {
                return [
                    'success' => false,
                    'provider' => 'yurtici',
                    'error' => 'Kimlik doğrulama başarısız',
                ];
            }

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/create-shipment", [
                    'cargoKey' => $this->customerCode,
                    'invoiceKey' => $shipmentData['order_id'],
                    'receiverName' => $shipmentData['recipient']['name'],
                    'receiverAddress' => $shipmentData['recipient']['address'],
                    'receiverPhone1' => $shipmentData['recipient']['phone'],
                    'receiverCityName' => $shipmentData['recipient']['city'],
                    'receiverTownName' => $shipmentData['recipient']['district'],
                    'senderName' => $shipmentData['sender']['name'],
                    'senderAddress' => $shipmentData['sender']['address'],
                    'senderPhone1' => $shipmentData['sender']['phone'],
                    'senderCityName' => $shipmentData['sender']['city'],
                    'senderTownName' => $shipmentData['sender']['district'],
                    'paymentType' => '1', // 1: Gönderici öder
                    'kg' => $shipmentData['weight'],
                    'pieceCount' => count($shipmentData['items']),
                    'description' => 'E-Ticaret',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'yurtici',
                    'tracking_code' => $data['outGuidList'][0] ?? null,
                    'shipment_id' => $data['outGuidList'][0] ?? null,
                ];
            }

            return [
                'success' => false,
                'provider' => 'yurtici',
                'error' => 'Gönderi oluşturulamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Yurtiçi Kargo gönderi oluşturma hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'yurtici',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function trackShipment(string $trackingCode): array
    {
        try {
            $token = $this->getAuthToken();
            
            if (!$token) {
                return [
                    'success' => false,
                    'provider' => 'yurtici',
                    'error' => 'Kimlik doğrulama başarısız',
                ];
            }

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/query-cargo", [
                    'keys' => [$trackingCode],
                    'keyType' => 2, // 2: Takip numarası
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['cargoMovements'])) {
                    $movements = $data['cargoMovements'][0];
                    return [
                        'success' => true,
                        'provider' => 'yurtici',
                        'tracking_code' => $trackingCode,
                        'status' => $movements['lastStatusDescription'] ?? 'Durum belirtilmedi',
                        'updated_at' => $movements['lastStatusDate'] ?? null,
                        'checkpoints' => $movements['movementList'] ?? [],
                    ];
                }
            }

            return [
                'success' => false,
                'provider' => 'yurtici',
                'error' => 'Takip bilgisi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Yurtiçi Kargo takip hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'yurtici',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelShipment(string $trackingCode): array
    {
        try {
            $token = $this->getAuthToken();
            
            if (!$token) {
                return [
                    'success' => false,
                    'provider' => 'yurtici',
                    'error' => 'Kimlik doğrulama başarısız',
                ];
            }

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/cancel-shipment", [
                    'shippingIds' => [$trackingCode],
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'provider' => 'yurtici',
                    'message' => 'Gönderi iptal edildi',
                ];
            }

            return [
                'success' => false,
                'provider' => 'yurtici',
                'error' => 'Gönderi iptal edilemedi',
            ];
        } catch (\Throwable $e) {
            Log::error('Yurtiçi Kargo iptal hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'yurtici',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getAuthToken(): ?string
    {
        try {
            $response = Http::post("{$this->baseUrl}/oauth/token", [
                'userName' => $this->username,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? null;
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('Yurtiçi Kargo token alma hatası', ['error' => $e->getMessage()]);
            return null;
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
