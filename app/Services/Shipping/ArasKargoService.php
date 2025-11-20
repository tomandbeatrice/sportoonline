<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SoapClient;

class ArasKargoService implements ShippingProviderInterface
{
    protected string $username;
    protected string $password;
    protected string $customerCode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->username = config('shipping.providers.aras.username');
        $this->password = config('shipping.providers.aras.password');
        $this->customerCode = config('shipping.providers.aras.customer_code');
        $this->baseUrl = config('shipping.providers.aras.base_url');
    }

    public function getQuote(array $shipmentData): array
    {
        try {
            // Aras Kargo REST API ile fiyat hesaplama
            $response = Http::post("{$this->baseUrl}/GetPrice", [
                'UserName' => $this->username,
                'Password' => $this->password,
                'CustomerCode' => $this->customerCode,
                'ReceiverCityName' => $shipmentData['destination']['city'],
                'ReceiverTownName' => $shipmentData['destination']['district'],
                'PaymentType' => 1, // 1: Gönderici öder
                'Weight' => $shipmentData['weight'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['IsSuccessful']) && $data['IsSuccessful']) {
                    return [
                        'success' => true,
                        'provider' => 'aras',
                        'price' => $data['UnitPrice'] ?? 0,
                        'currency' => 'TRY',
                        'estimated_delivery_days' => 2,
                    ];
                }
            }

            return [
                'success' => false,
                'provider' => 'aras',
                'error' => 'Fiyat teklifi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Aras Kargo fiyat teklifi hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'aras',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createShipment(array $shipmentData): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/CreateOrder", [
                'UserName' => $this->username,
                'Password' => $this->password,
                'CustomerCode' => $this->customerCode,
                'ReceiverName' => $shipmentData['recipient']['name'],
                'ReceiverAddress' => $shipmentData['recipient']['address'],
                'ReceiverPhone1' => $shipmentData['recipient']['phone'],
                'ReceiverCityName' => $shipmentData['recipient']['city'],
                'ReceiverTownName' => $shipmentData['recipient']['district'],
                'SenderName' => $shipmentData['sender']['name'],
                'SenderAddress' => $shipmentData['sender']['address'],
                'SenderPhone1' => $shipmentData['sender']['phone'],
                'SenderCityName' => $shipmentData['sender']['city'],
                'SenderTownName' => $shipmentData['sender']['district'],
                'PaymentType' => 1,
                'Weight' => $shipmentData['weight'],
                'PieceCount' => count($shipmentData['items']),
                'InvoiceAmount' => $shipmentData['invoice_amount'] ?? 0,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['IsSuccessful']) && $data['IsSuccessful']) {
                    return [
                        'success' => true,
                        'provider' => 'aras',
                        'tracking_code' => $data['OrderNo'] ?? null,
                        'shipment_id' => $data['OrderId'] ?? null,
                    ];
                }
            }

            return [
                'success' => false,
                'provider' => 'aras',
                'error' => 'Gönderi oluşturulamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Aras Kargo gönderi oluşturma hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'aras',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function trackShipment(string $trackingCode): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/GetOrderStatus", [
                'UserName' => $this->username,
                'Password' => $this->password,
                'OrderNo' => $trackingCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['IsSuccessful']) && $data['IsSuccessful']) {
                    return [
                        'success' => true,
                        'provider' => 'aras',
                        'tracking_code' => $trackingCode,
                        'status' => $data['StatusDescription'] ?? 'Durum belirtilmedi',
                        'updated_at' => $data['LastStatusDate'] ?? null,
                        'checkpoints' => $data['TrackingDetails'] ?? [],
                    ];
                }
            }

            return [
                'success' => false,
                'provider' => 'aras',
                'error' => 'Takip bilgisi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('Aras Kargo takip hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'aras',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelShipment(string $trackingCode): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/CancelOrder", [
                'UserName' => $this->username,
                'Password' => $this->password,
                'OrderNo' => $trackingCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['IsSuccessful']) && $data['IsSuccessful']) {
                    return [
                        'success' => true,
                        'provider' => 'aras',
                        'message' => 'Gönderi iptal edildi',
                    ];
                }
            }

            return [
                'success' => false,
                'provider' => 'aras',
                'error' => 'Gönderi iptal edilemedi',
            ];
        } catch (\Throwable $e) {
            Log::error('Aras Kargo iptal hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'aras',
                'error' => $e->getMessage(),
            ];
        }
    }
}
