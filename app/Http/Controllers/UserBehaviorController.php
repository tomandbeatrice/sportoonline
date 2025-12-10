<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserBehaviorService;

class UserBehaviorController extends Controller
{
    protected UserBehaviorService $behaviorService;

    public function __construct(UserBehaviorService $behaviorService)
    {
        $this->behaviorService = $behaviorService;
    }

    /**
     * Ürün görüntüleme kaydı
     */
    public function trackProductView(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'source' => 'nullable|string|max:50',
            'referrer' => 'nullable|string|max:255',
        ]);

        $this->behaviorService->trackProductView(
            $validated['product_id'],
            $validated['source'] ?? null,
            $validated['referrer'] ?? null
        );

        return response()->json(['success' => true]);
    }

    /**
     * Görüntüleme süresini güncelle
     */
    public function updateViewDuration(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'duration' => 'required|integer|min:1|max:3600',
        ]);

        $this->behaviorService->updateViewDuration(
            $validated['product_id'],
            $validated['duration']
        );

        return response()->json(['success' => true]);
    }

    /**
     * Kampanya görüntüleme kaydı
     */
    public function trackCampaignView(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'placement' => 'nullable|string|max:50',
        ]);

        $this->behaviorService->trackCampaignView(
            $validated['campaign_id'],
            $validated['placement'] ?? null
        );

        return response()->json(['success' => true]);
    }

    /**
     * Kampanya tıklama kaydı
     */
    public function trackCampaignClick(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'click_target' => 'nullable|string|max:50',
        ]);

        $this->behaviorService->trackCampaignClick(
            $validated['campaign_id'],
            $validated['click_target'] ?? null
        );

        return response()->json(['success' => true]);
    }

    /**
     * Arama kaydı
     */
    public function trackSearch(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|max:255',
            'results_count' => 'required|integer|min:0',
        ]);

        $searchId = $this->behaviorService->trackSearch(
            $validated['query'],
            $validated['results_count']
        );

        return response()->json([
            'success' => true,
            'search_id' => $searchId,
        ]);
    }

    /**
     * Arama sonucuna tıklama
     */
    public function trackSearchClick(Request $request)
    {
        $validated = $request->validate([
            'search_id' => 'required|integer',
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $this->behaviorService->trackSearchClick(
            $validated['search_id'],
            $validated['product_id']
        );

        return response()->json(['success' => true]);
    }

    /**
     * Sepet olayı kaydı
     */
    public function trackCartEvent(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'event_type' => 'required|in:add,remove,update_quantity',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'source' => 'nullable|string|max:50',
        ]);

        $this->behaviorService->trackCartEvent(
            $validated['product_id'],
            $validated['event_type'],
            $validated['quantity'],
            $validated['price'],
            $validated['source'] ?? null
        );

        return response()->json(['success' => true]);
    }

    /**
     * Son görüntülemeler
     */
    public function getRecentViews(Request $request)
    {
        $limit = min($request->input('limit', 10), 50);
        $views = $this->behaviorService->getRecentViews(auth()->id(), $limit);

        return response()->json([
            'recent_views' => $views,
        ]);
    }

    /**
     * Popüler aramalar
     */
    public function getPopularSearches(Request $request)
    {
        $limit = min($request->input('limit', 10), 50);
        $searches = $this->behaviorService->getPopularSearches($limit);

        return response()->json([
            'popular_searches' => $searches,
        ]);
    }

    /**
     * Trend ürünler
     */
    public function getTrendingProducts(Request $request)
    {
        $limit = min($request->input('limit', 10), 50);
        $products = $this->behaviorService->getTrendingProducts($limit);

        return response()->json([
            'trending_products' => $products,
        ]);
    }
}
