<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ShippingFee;
use Illuminate\Http\Request;

class ShippingFeeController extends Controller
{
    /**
     * Get seller's shipping fees
     */
    public function index()
    {
        $seller = auth()->user();
        
        $shippingFees = ShippingFee::where('user_id', $seller->id)->get();
        $regions = ShippingFee::getRegions();

        return response()->json([
            'shipping_fees' => $shippingFees,
            'available_regions' => $regions,
        ]);
    }

    /**
     * Create or update shipping fee for region
     */
    public function upsert(Request $request)
    {
        $request->validate([
            'region' => 'required|string',
            'fee' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $seller = auth()->user();

        $shippingFee = ShippingFee::updateOrCreate(
            [
                'user_id' => $seller->id,
                'region' => $request->region,
            ],
            [
                'fee' => $request->fee,
                'free_shipping_threshold' => $request->free_shipping_threshold ?? 0,
                'is_active' => $request->is_active ?? true,
            ]
        );

        return response()->json([
            'message' => 'Kargo ücreti kaydedildi',
            'shipping_fee' => $shippingFee,
        ]);
    }

    /**
     * Set default shipping fees for all regions
     */
    public function setDefaults(Request $request)
    {
        $request->validate([
            'default_fee' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
        ]);

        $seller = auth()->user();
        $regions = array_keys(ShippingFee::getRegions());
        
        $created = [];
        foreach ($regions as $region) {
            $shippingFee = ShippingFee::updateOrCreate(
                [
                    'user_id' => $seller->id,
                    'region' => $region,
                ],
                [
                    'fee' => $request->default_fee,
                    'free_shipping_threshold' => $request->free_shipping_threshold ?? 0,
                    'is_active' => true,
                ]
            );
            $created[] = $shippingFee;
        }

        return response()->json([
            'message' => 'Tüm bölgeler için varsayılan kargo ücreti ayarlandı',
            'shipping_fees' => $created,
        ]);
    }

    /**
     * Toggle shipping fee active status
     */
    public function toggle(int $id)
    {
        $seller = auth()->user();
        
        $shippingFee = ShippingFee::where('user_id', $seller->id)
            ->findOrFail($id);

        $shippingFee->update([
            'is_active' => !$shippingFee->is_active,
        ]);

        return response()->json([
            'message' => 'Durum güncellendi',
            'shipping_fee' => $shippingFee,
        ]);
    }

    /**
     * Delete shipping fee
     */
    public function destroy(int $id)
    {
        $seller = auth()->user();
        
        $shippingFee = ShippingFee::where('user_id', $seller->id)
            ->findOrFail($id);

        $shippingFee->delete();

        return response()->json([
            'message' => 'Kargo ücreti silindi',
        ]);
    }

    /**
     * Calculate shipping for specific order
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $seller = auth()->user();
        $region = ShippingFee::getCityRegion($request->city);
        
        $shippingFee = ShippingFee::where('user_id', $seller->id)
            ->where('region', $region)
            ->where('is_active', true)
            ->first();

        if (!$shippingFee) {
            return response()->json([
                'fee' => 30.00, // Default
                'region' => $region,
                'free_shipping' => false,
                'message' => 'Bu bölge için kargo ücreti belirlenmemiş, varsayılan ücret uygulanacak',
            ]);
        }

        $fee = $shippingFee->calculateFee($request->subtotal);
        $freeShipping = $shippingFee->qualifiesForFreeShipping($request->subtotal);

        return response()->json([
            'fee' => $fee,
            'region' => $region,
            'free_shipping' => $freeShipping,
            'threshold' => $shippingFee->free_shipping_threshold,
            'message' => $freeShipping ? 'Ücretsiz kargo uygulandı!' : null,
        ]);
    }
}
