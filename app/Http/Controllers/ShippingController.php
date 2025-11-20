<?php

namespace App\Http\Controllers;

use App\Services\ShippingService;
use App\Models\Order;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    protected ShippingService $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function getProviders()
    {
        $providers = $this->shippingService->getAvailableProviders();

        return response()->json([
            'success' => true,
            'providers' => $providers,
        ]);
    }

    public function getQuotes(Request $request)
    {
        $request->validate([
            'origin.city' => 'required|string',
            'destination.city' => 'required|string',
            'destination.district' => 'required|string',
            'weight' => 'required|numeric|min:0.1',
            'dimensions' => 'nullable|array',
            'dimensions.length' => 'nullable|numeric',
            'dimensions.width' => 'nullable|numeric',
            'dimensions.height' => 'nullable|numeric',
        ]);

        $quotes = $this->shippingService->getQuotes($request->all());

        return response()->json([
            'success' => true,
            'quotes' => $quotes,
        ]);
    }

    public function createShipment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'provider' => 'nullable|string|in:geliver,aras,surat,ptt,yurtici',
        ]);

        $order = Order::with(['items', 'vendor'])->findOrFail($request->order_id);

        // Sadece satıcı kendi siparişleri için kargo oluşturabilir
        if (auth()->user()->role === 'seller' && $order->vendor_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'error' => 'Bu siparişe erişim yetkiniz yok',
            ], 403);
        }

        $result = $this->shippingService->createShipment($order, $request->provider);

        return response()->json($result);
    }

    public function track(Request $request, string $trackingCode)
    {
        $provider = $request->input('provider');
        
        $result = $this->shippingService->trackShipment($trackingCode, $provider);

        return response()->json($result);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string',
            'provider' => 'required|string|in:geliver,aras,surat,ptt,yurtici',
        ]);

        $result = $this->shippingService->cancelShipment(
            $request->tracking_code,
            $request->provider
        );

        return response()->json($result);
    }
}