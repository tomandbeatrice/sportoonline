<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShippingService
{
    public function createShipment($order)
    {
        $response = Http::post('https://api.kargo-provider.com/create', [
            'order_id' => $order->id,
            'address' => $order->address,
            'recipient' => $order->user->name,
            'phone' => $order->user->phone,
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                ];
            }),
        ]);

        if ($response->successful()) {
            $order->update(['status' => 'Kargoya Verildi']);
        }

        return $response->json();
    }
}