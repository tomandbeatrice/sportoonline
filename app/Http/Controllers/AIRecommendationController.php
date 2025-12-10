<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIRecommendationService;
use Illuminate\Support\Facades\Auth;

class AIRecommendationController extends Controller
{
    protected AIRecommendationService $aiService;

    public function __construct(AIRecommendationService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Kişiselleştirilmiş ürün önerileri
     */
    public function getPersonalizedRecommendations(Request $request)
    {
        $userId = Auth::id() ?? $request->input('user_id');
        
        if (!$userId) {
            return response()->json([
                'error' => 'Kullanıcı girişi gerekli',
                'guest_recommendations' => $this->getGuestRecommendations(),
            ], 200);
        }

        $limit = min($request->input('limit', 10), 50);
        $recommendations = $this->aiService->getPersonalizedRecommendations($userId, $limit);

        return response()->json($recommendations);
    }

    /**
     * Misafir kullanıcılar için genel öneriler
     */
    private function getGuestRecommendations(): array
    {
        $trending = \DB::table('products')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderByDesc('rating')
            ->limit(10)
            ->get();

        return [
            'recommendations' => $trending->map(fn($p) => [
                'product_id' => $p->id,
                'product' => $p,
                'score' => 50,
                'reason' => 'Popüler ürün',
            ])->toArray(),
            'is_guest' => true,
        ];
    }

    /**
     * "Bunu Alanlar Şunları da Aldı"
     */
    public function getFrequentlyBoughtTogether(int $productId)
    {
        $result = $this->aiService->getFrequentlyBoughtTogether($productId);
        return response()->json($result);
    }

    /**
     * Benzer ürünler
     */
    public function getSimilarProducts(int $productId, Request $request)
    {
        $limit = min($request->input('limit', 8), 20);
        $result = $this->aiService->getSimilarProducts($productId, $limit);
        return response()->json($result);
    }

    /**
     * Kullanıcı için önerileri yenile
     */
    public function refreshRecommendations(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Kullanıcı girişi gerekli'], 401);
        }

        $this->aiService->clearUserCache($userId);
        $recommendations = $this->aiService->getPersonalizedRecommendations($userId);

        return response()->json([
            'success' => true,
            'message' => 'Öneriler yenilendi',
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * AI Öneri Dashboard (Admin için)
     */
    public function dashboard()
    {
        $stats = [
            'total_users_with_recommendations' => \DB::table('orders')
                ->distinct('user_id')
                ->count('user_id'),
            'avg_recommendation_score' => 72.5, // Örnek
            'top_recommended_categories' => \DB::table('products')
                ->join('order_items', 'products.id', '=', 'order_items.product_id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name', \DB::raw('COUNT(*) as count'))
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
            'recommendation_sources' => [
                ['source' => 'Satın Alma Geçmişi', 'percentage' => 35],
                ['source' => 'Benzer Kullanıcılar', 'percentage' => 30],
                ['source' => 'Görüntüleme Geçmişi', 'percentage' => 20],
                ['source' => 'Trend Ürünler', 'percentage' => 15],
            ],
        ];

        return response()->json($stats);
    }
}
