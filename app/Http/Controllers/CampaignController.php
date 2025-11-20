<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    /**
     * List seller's campaigns
     */
    public function index(Request $request)
    {
        $seller = $this->ensureSeller($request);

        $campaigns = Campaign::where('seller_id', $seller->id)
            ->withCount('usages')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($campaigns);
    }

    /**
     * Create new campaign
     */
    public function store(Request $request)
    {
        $seller = $this->ensureSeller($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,buy_x_get_y,free_shipping',
            'discount_value' => 'required|numeric|min:0',
            'min_quantity' => 'nullable|integer|min:1',
            'free_quantity' => 'nullable|integer|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50|unique:campaigns,coupon_code',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_customer' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'applies_to' => 'required|in:all_products,specific_products,specific_categories',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        // Auto-generate coupon code if not provided
        if (empty($data['coupon_code'])) {
            $data['coupon_code'] = $this->generateCouponCode();
        } else {
            $data['coupon_code'] = strtoupper($data['coupon_code']);
        }

        // Validate percentage discount
        if ($data['type'] === 'percentage' && $data['discount_value'] > 100) {
            return response()->json(['error' => 'Yüzde indirimi 100\'den fazla olamaz'], 422);
        }

        $campaign = Campaign::create(array_merge($data, [
            'seller_id' => $seller->id,
            'used_count' => 0,
        ]));

        return response()->json($campaign, 201);
    }

    /**
     * Show campaign details
     */
    public function show(Request $request, Campaign $campaign)
    {
        $seller = $this->ensureSeller($request);
        $this->authorizeCampaign($seller, $campaign);

        $campaign->load(['usages.user', 'usages.order']);

        return response()->json($campaign);
    }

    /**
     * Update campaign
     */
    public function update(Request $request, Campaign $campaign)
    {
        $seller = $this->ensureSeller($request);
        $this->authorizeCampaign($seller, $campaign);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:percentage,fixed,buy_x_get_y,free_shipping',
            'discount_value' => 'sometimes|numeric|min:0',
            'min_quantity' => 'nullable|integer|min:1',
            'free_quantity' => 'nullable|integer|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50|unique:campaigns,coupon_code,' . $campaign->id,
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_customer' => 'sometimes|integer|min:1',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'is_active' => 'boolean',
            'applies_to' => 'sometimes|in:all_products,specific_products,specific_categories',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        if (isset($data['coupon_code'])) {
            $data['coupon_code'] = strtoupper($data['coupon_code']);
        }

        if (isset($data['type']) && $data['type'] === 'percentage' && ($data['discount_value'] ?? $campaign->discount_value) > 100) {
            return response()->json(['error' => 'Yüzde indirimi 100\'den fazla olamaz'], 422);
        }

        $campaign->update($data);

        return response()->json($campaign);
    }

    /**
     * Delete campaign
     */
    public function destroy(Request $request, Campaign $campaign)
    {
        $seller = $this->ensureSeller($request);
        $this->authorizeCampaign($seller, $campaign);

        $campaign->delete();

        return response()->json(['message' => 'Kampanya silindi']);
    }

    /**
     * Toggle campaign active status
     */
    public function toggleActive(Request $request, Campaign $campaign)
    {
        $seller = $this->ensureSeller($request);
        $this->authorizeCampaign($seller, $campaign);

        $campaign->update(['is_active' => !$campaign->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $campaign->is_active,
        ]);
    }

    /**
     * Validate and apply coupon code
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'cart_total' => 'required|numeric|min:0',
            'product_ids' => 'nullable|array',
        ]);

        $couponCode = strtoupper($request->coupon_code);
        
        $campaign = Campaign::where('coupon_code', $couponCode)
            ->active()
            ->available()
            ->first();

        if (!$campaign) {
            return response()->json(['error' => 'Geçersiz veya süresi dolmuş kupon kodu'], 404);
        }

        if (!$campaign->canBeUsedBy(auth()->id())) {
            return response()->json(['error' => 'Bu kuponu kullanma limitiniz doldu'], 403);
        }

        if ($request->cart_total < $campaign->min_purchase_amount) {
            return response()->json([
                'error' => 'Minimum sepet tutarı ₺' . number_format($campaign->min_purchase_amount, 2)
            ], 422);
        }

        $discount = $campaign->calculateDiscount($request->cart_total);

        return response()->json([
            'valid' => true,
            'campaign' => $campaign,
            'discount_amount' => $discount,
            'final_amount' => max(0, $request->cart_total - $discount),
        ]);
    }

    /**
     * Get campaign statistics
     */
    public function stats(Request $request, Campaign $campaign)
    {
        $seller = $this->ensureSeller($request);
        $this->authorizeCampaign($seller, $campaign);

        $usages = $campaign->usages()->with(['user', 'order'])->get();
        
        $stats = [
            'total_usage' => $usages->count(),
            'total_discount_given' => $usages->sum('discount_amount'),
            'unique_customers' => $usages->pluck('user_id')->unique()->count(),
            'avg_discount_per_order' => $usages->avg('discount_amount'),
            'usage_by_date' => $usages->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            })->map->count(),
        ];

        return response()->json($stats);
    }

    private function ensureSeller(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->isSeller(), 403, 'Bu işlem yalnızca satıcılar tarafından yapılabilir.');
        return $user;
    }

    private function authorizeCampaign($seller, Campaign $campaign)
    {
        abort_if($campaign->seller_id !== $seller->id, 403, 'Bu kampanyayı yönetme yetkiniz yok.');
    }

    private function generateCouponCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Campaign::where('coupon_code', $code)->exists());

        return $code;
    }
}
