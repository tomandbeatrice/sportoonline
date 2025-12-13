<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Services\Shipping\GeliverService;
use App\Services\Shipping\ArasKargoService;
use App\Services\Shipping\SuratKargoService;
use App\Services\Shipping\PTTKargoService;
use App\Services\Shipping\YurticiKargoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
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

    /**
     * Generate shipping label PDF for return request
     */
    public function generateReturnLabel(ReturnRequest $returnRequest): string
    {
        $labelData = $this->prepareReturnLabelData($returnRequest);
        
        // Generate PDF
        $pdf = Pdf::loadView('pdf.return-shipping-label', $labelData);
        
        // Generate filename
        $filename = 'return-labels/' . $returnRequest->return_number . '.pdf';
        
        // Save to storage
        Storage::put('public/' . $filename, $pdf->output());
        
        return Storage::url($filename);
    }

    /**
     * Generate shipping label PDF for order
     */
    public function generateOrderLabel(Order $order): string
    {
        $labelData = $this->prepareOrderLabelData($order);
        
        // Generate PDF
        $pdf = Pdf::loadView('pdf.order-shipping-label', $labelData);
        
        // Generate filename
        $filename = 'shipping-labels/order-' . $order->id . '.pdf';
        
        // Save to storage
        Storage::put('public/' . $filename, $pdf->output());
        
        return Storage::url($filename);
    }

    /**
     * Prepare data for return label
     */
    protected function prepareReturnLabelData(ReturnRequest $returnRequest): array
    {
        $order = $returnRequest->order;
        $user = $returnRequest->user;
        
        return [
            'return_number' => $returnRequest->return_number,
            'order_id' => $order->id,
            'tracking_number' => $returnRequest->tracking_number ?? 'Bekliyor',
            'barcode' => $this->generateBarcode($returnRequest->return_number),
            
            // Sender (Customer)
            'sender_name' => $user->name,
            'sender_address' => $order->shipping_address,
            'sender_city' => $order->shipping_city,
            'sender_district' => $order->shipping_district,
            'sender_postal_code' => $order->shipping_postal_code ?? '',
            'sender_phone' => $user->phone,
            
            // Receiver (Vendor/Warehouse)
            'receiver_name' => $returnRequest->vendor->business_name ?? $returnRequest->vendor->name ?? 'Sportoonline',
            'receiver_address' => $returnRequest->vendor->warehouse_address ?? config('app.warehouse_address', 'Varsayılan Adres'),
            'receiver_city' => $returnRequest->vendor->city ?? config('app.warehouse_city', 'İstanbul'),
            'receiver_district' => $returnRequest->vendor->district ?? config('app.warehouse_district', 'Şişli'),
            'receiver_postal_code' => $returnRequest->vendor->postal_code ?? '',
            'receiver_phone' => $returnRequest->vendor->phone ?? config('app.warehouse_phone', '05001234567'),
            
            // Product info
            'product_name' => $returnRequest->orderItem?->product?->name ?? 'İade Ürünü',
            'quantity' => $returnRequest->quantity,
            
            // Dates
            'created_at' => $returnRequest->created_at,
            'generated_at' => now(),
        ];
    }

    /**
     * Prepare data for order shipping label
     */
    protected function prepareOrderLabelData(Order $order): array
    {
        return [
            'order_id' => $order->id,
            'tracking_number' => $order->tracking_number ?? 'Bekliyor',
            'barcode' => $this->generateBarcode('ORD' . $order->id),
            
            // Sender (Vendor/Warehouse)
            'sender_name' => $order->vendor->business_name ?? $order->vendor->name ?? 'Sportoonline',
            'sender_address' => $order->vendor->warehouse_address ?? config('app.warehouse_address', 'Varsayılan Adres'),
            'sender_city' => $order->vendor->city ?? config('app.warehouse_city', 'İstanbul'),
            'sender_district' => $order->vendor->district ?? config('app.warehouse_district', 'Şişli'),
            'sender_postal_code' => $order->vendor->postal_code ?? '',
            'sender_phone' => $order->vendor->phone ?? config('app.warehouse_phone', '05001234567'),
            
            // Receiver (Customer)
            'receiver_name' => $order->user->name,
            'receiver_address' => $order->shipping_address,
            'receiver_city' => $order->shipping_city,
            'receiver_district' => $order->shipping_district,
            'receiver_postal_code' => $order->shipping_postal_code ?? '',
            'receiver_phone' => $order->user->phone,
            
            // Order info
            'item_count' => $order->orderItems->count(),
            'total_weight' => $order->orderItems->sum(function($item) {
                return ($item->product->weight ?? 0) * $item->quantity;
            }),
            
            // Dates
            'created_at' => $order->created_at,
            'generated_at' => now(),
        ];
    }

    /**
     * Generate barcode data for label
     */
    protected function generateBarcode(string $code): string
    {
        // Simple barcode representation
        // In production, you might want to use a proper barcode library
        return '*' . strtoupper($code) . '*';
    }
}