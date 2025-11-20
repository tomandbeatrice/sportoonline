<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PTTKargoService implements ShippingProviderInterface
{
    protected string $username;
    protected string $password;
    protected string $customerCode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->username = config('shipping.providers.ptt.username');
        $this->password = config('shipping.providers.ptt.password');
        $this->customerCode = config('shipping.providers.ptt.customer_code');
        $this->baseUrl = config('shipping.providers.ptt.base_url');
    }

    public function getQuote(array $shipmentData): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode("{$this->username}:{$this->password}"),
            ])->post("{$this->baseUrl}/ucret-hesapla", [
                'musteri_kodu' => $this->customerCode,
                'gonderen_il' => $shipmentData['origin']['city'],
                'alici_il' => $shipmentData['destination']['city'],
                'alici_ilce' => $shipmentData['destination']['district'],
                'agirlik' => $shipmentData['weight'],
                'odemeTipi' => 1, // 1: Gönderici öder
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'ptt',
                    'price' => $data['ucret'] ?? 0,
                    'currency' => 'TRY',
                    'estimated_delivery_days' => $data['tahmini_teslimat_gunu'] ?? 3,
                ];
            }

            return [
                'success' => false,
                'provider' => 'ptt',
                'error' => 'Fiyat teklifi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('PTT Kargo fiyat teklifi hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'ptt',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createShipment(array $shipmentData): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode("{$this->username}:{$this->password}"),
            ])->post("{$this->baseUrl}/gonderi-olustur", [
                'musteri_kodu' => $this->customerCode,
                'alici' => [
                    'adi_soyadi' => $shipmentData['recipient']['name'],
                    'adres' => $shipmentData['recipient']['address'],
                    'telefon' => $shipmentData['recipient']['phone'],
                    'il' => $shipmentData['recipient']['city'],
                    'ilce' => $shipmentData['recipient']['district'],
                ],
                'gonderen' => [
                    'adi_soyadi' => $shipmentData['sender']['name'],
                    'adres' => $shipmentData['sender']['address'],
                    'telefon' => $shipmentData['sender']['phone'],
                    'il' => $shipmentData['sender']['city'],
                    'ilce' => $shipmentData['sender']['district'],
                ],
                'agirlik' => $shipmentData['weight'],
                'adet' => count($shipmentData['items']),
                'odemeTipi' => 1,
                'icerik' => 'E-Ticaret Ürünleri',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'ptt',
                    'tracking_code' => $data['barkod_no'] ?? null,
                    'shipment_id' => $data['gonderi_id'] ?? null,
                ];
            }

            return [
                'success' => false,
                'provider' => 'ptt',
                'error' => 'Gönderi oluşturulamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('PTT Kargo gönderi oluşturma hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'ptt',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function trackShipment(string $trackingCode): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode("{$this->username}:{$this->password}"),
            ])->get("{$this->baseUrl}/gonderi-sorgula", [
                'barkod_no' => $trackingCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'ptt',
                    'tracking_code' => $trackingCode,
                    'status' => $data['durum_aciklama'] ?? 'Durum belirtilmedi',
                    'updated_at' => $data['son_guncelleme'] ?? null,
                    'checkpoints' => $data['hareketler'] ?? [],
                ];
            }

            return [
                'success' => false,
                'provider' => 'ptt',
                'error' => 'Takip bilgisi alınamadı',
            ];
        } catch (\Throwable $e) {
            Log::error('PTT Kargo takip hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'ptt',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelShipment(string $trackingCode): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode("{$this->username}:{$this->password}"),
            ])->post("{$this->baseUrl}/gonderi-iptal", [
                'barkod_no' => $trackingCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'provider' => 'ptt',
                    'message' => 'Gönderi iptal edildi',
                ];
            }

            return [
                'success' => false,
                'provider' => 'ptt',
                'error' => 'Gönderi iptal edilemedi',
            ];
        } catch (\Throwable $e) {
            Log::error('PTT Kargo iptal hatası', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'ptt',
                'error' => $e->getMessage(),
            ];
        }
    }
}
