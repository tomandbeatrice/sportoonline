<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\TurboCoupon;
use App\Services\TurboCompetitionService;
use Illuminate\Http\Request;

class TurboCouponController extends Controller
{
    protected $turboService;

    public function __construct(TurboCompetitionService $turboService)
    {
        $this->turboService = $turboService;
    }

    /**
     * Get seller's turbo coupons
     */
    public function index(Request $request)
    {
        try {
            $sellerId = $request->user()->id;

            $coupons = TurboCoupon::where('seller_id', $sellerId)
                ->with(['winner.competition', 'usage'])
                ->orderByDesc('created_at')
                ->get()
                ->map(function($coupon) {
                    return [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'commission_discount_percent' => $coupon->commission_discount_percent,
                        'min_order_amount' => $coupon->min_order_amount,
                        'max_uses' => $coupon->max_uses,
                        'used_count' => $coupon->used_count,
                        'remaining_uses' => $coupon->remaining_uses,
                        'valid_from' => $coupon->valid_from->format('Y-m-d'),
                        'valid_until' => $coupon->valid_until->format('Y-m-d'),
                        'is_active' => $coupon->is_active,
                        'status_label' => $coupon->status_label,
                        'is_expired' => $coupon->is_expired,
                        'conditions' => $coupon->conditions,
                        'total_savings' => $coupon->usage->sum('savings'),
                        'competition' => $coupon->winner ? [
                            'month' => $coupon->winner->competition->month,
                            'year' => $coupon->winner->competition->year,
                            'rank' => $coupon->winner->rank,
                            'rank_badge' => $coupon->winner->rank_badge
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $coupons
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kuponlar alınamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle coupon active status
     */
    public function toggle(Request $request, $id)
    {
        try {
            $sellerId = $request->user()->id;
            
            $coupon = TurboCoupon::where('id', $id)
                ->where('seller_id', $sellerId)
                ->firstOrFail();

            $coupon->update([
                'is_active' => !$coupon->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => $coupon->is_active ? 'Kupon aktif edildi.' : 'Kupon pasif edildi.',
                'data' => [
                    'id' => $coupon->id,
                    'is_active' => $coupon->is_active
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon durumu değiştirilemedi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get coupon usage details
     */
    public function usage(Request $request, $id)
    {
        try {
            $sellerId = $request->user()->id;
            
            $coupon = TurboCoupon::where('id', $id)
                ->where('seller_id', $sellerId)
                ->with(['usage.order'])
                ->firstOrFail();

            $usageDetails = $coupon->usage->map(function($usage) {
                return [
                    'id' => $usage->id,
                    'order_id' => $usage->order_id,
                    'order_date' => $usage->order->created_at->format('Y-m-d H:i'),
                    'original_commission' => $usage->original_commission,
                    'discounted_commission' => $usage->discounted_commission,
                    'savings' => $usage->savings,
                    'used_at' => $usage->created_at->format('Y-m-d H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'coupon' => [
                        'code' => $coupon->code,
                        'commission_discount_percent' => $coupon->commission_discount_percent,
                        'used_count' => $coupon->used_count,
                        'max_uses' => $coupon->max_uses
                    ],
                    'usage' => $usageDetails,
                    'total_savings' => $usageDetails->sum('savings')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanım detayları alınamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate and check coupon
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'nullable|numeric|min:0'
        ]);

        try {
            $coupon = TurboCoupon::where('code', $request->code)
                ->where('seller_id', $request->user()->id)
                ->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kupon bulunamadı veya size ait değil.'
                ], 404);
            }

            $validation = $coupon->isValid($request->order_amount);

            return response()->json([
                'success' => $validation['valid'],
                'message' => $validation['message'] ?? 'Kupon geçerli.',
                'data' => $validation['valid'] ? [
                    'code' => $coupon->code,
                    'commission_discount_percent' => $coupon->commission_discount_percent,
                    'remaining_uses' => $coupon->remaining_uses,
                    'valid_until' => $coupon->valid_until->format('Y-m-d')
                ] : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon doğrulanamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
