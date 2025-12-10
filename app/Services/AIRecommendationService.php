<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class AIRecommendationService
{
    /**
     * Kullanıcı için kişiselleştirilmiş ürün önerileri
     */
    public function getPersonalizedRecommendations(int $userId, int $limit = 10): array
    {
        $cacheKey = "ai.recommendations.user.{$userId}";
        
        return Cache::remember($cacheKey, 1800, function () use ($userId, $limit) {
            $userProfile = $this->buildUserProfile($userId);
            $recommendations = [];

            // 1. Satın alma geçmişine dayalı öneriler
            $purchaseBasedRecs = $this->getPurchaseBasedRecommendations($userId, $userProfile);
            
            // 2. Benzer kullanıcıların aldıkları (Collaborative Filtering)
            $collaborativeRecs = $this->getCollaborativeRecommendations($userId, $userProfile);
            
            // 3. Görüntüleme geçmişine dayalı öneriler
            $viewBasedRecs = $this->getViewBasedRecommendations($userId);
            
            // 4. Trend ürünler (kullanıcı profiline uygun)
            $trendingRecs = $this->getTrendingRecommendations($userProfile);

            // Önerileri birleştir ve skorla
            $allRecs = collect([
                ...$purchaseBasedRecs->map(fn($r) => [...$r, 'source' => 'purchase', 'weight' => 0.35]),
                ...$collaborativeRecs->map(fn($r) => [...$r, 'source' => 'collaborative', 'weight' => 0.30]),
                ...$viewBasedRecs->map(fn($r) => [...$r, 'source' => 'view_history', 'weight' => 0.20]),
                ...$trendingRecs->map(fn($r) => [...$r, 'source' => 'trending', 'weight' => 0.15]),
            ]);

            // Tekrar edenleri birleştir ve skorlarını topla
            $merged = $allRecs->groupBy('product_id')->map(function ($group) {
                $first = $group->first();
                $totalScore = $group->sum(fn($item) => ($item['score'] ?? 50) * $item['weight']);
                $sources = $group->pluck('source')->unique()->values()->toArray();
                
                return [
                    'product_id' => $first['product_id'],
                    'product' => $first['product'] ?? null,
                    'score' => round($totalScore, 2),
                    'sources' => $sources,
                    'reason' => $this->generateReasonText($sources),
                ];
            })->sortByDesc('score')->take($limit)->values();

            return [
                'user_id' => $userId,
                'recommendations' => $merged->toArray(),
                'profile_summary' => $this->getProfileSummary($userProfile),
                'generated_at' => now()->toISOString(),
            ];
        });
    }

    /**
     * Kullanıcı profili oluştur
     */
    private function buildUserProfile(int $userId): array
    {
        // Satın alma geçmişi
        $purchases = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.user_id', $userId)
            ->select('products.category_id', 'products.price', 'order_items.quantity')
            ->get();

        // Favori kategoriler
        $categoryPreferences = $purchases->groupBy('category_id')
            ->map(fn($items) => $items->sum('quantity'))
            ->sortDesc()
            ->take(5);

        // Fiyat aralığı tercihi
        $avgPrice = $purchases->avg('price') ?? 0;
        $minPrice = $purchases->min('price') ?? 0;
        $maxPrice = $purchases->max('price') ?? 10000;

        // Görüntüleme geçmişi
        $views = DB::table('product_views')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->pluck('product_id');

        // Favoriler
        $favorites = DB::table('favorites')
            ->where('user_id', $userId)
            ->pluck('product_id');

        return [
            'user_id' => $userId,
            'category_preferences' => $categoryPreferences->toArray(),
            'price_range' => [
                'avg' => $avgPrice,
                'min' => $minPrice,
                'max' => $maxPrice,
            ],
            'recent_views' => $views->toArray(),
            'favorites' => $favorites->toArray(),
            'total_orders' => $purchases->count(),
        ];
    }

    /**
     * Satın alma geçmişine dayalı öneriler
     */
    private function getPurchaseBasedRecommendations(int $userId, array $profile): Collection
    {
        if (empty($profile['category_preferences'])) {
            return collect();
        }

        $categoryIds = array_keys($profile['category_preferences']);
        $purchasedProductIds = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->pluck('order_items.product_id')
            ->toArray();

        return DB::table('products')
            ->whereIn('category_id', $categoryIds)
            ->whereNotIn('id', $purchasedProductIds)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->whereBetween('price', [
                $profile['price_range']['min'] * 0.5,
                $profile['price_range']['max'] * 1.5
            ])
            ->orderByDesc('rating')
            ->limit(20)
            ->get()
            ->map(function ($product) use ($profile) {
                $categoryWeight = $profile['category_preferences'][$product->category_id] ?? 1;
                return [
                    'product_id' => $product->id,
                    'product' => $product,
                    'score' => min(100, 50 + ($categoryWeight * 5) + ($product->rating * 5)),
                ];
            });
    }

    /**
     * Collaborative Filtering - Benzer kullanıcıların aldıkları
     */
    private function getCollaborativeRecommendations(int $userId, array $profile): Collection
    {
        // Benzer kullanıcıları bul (aynı ürünleri alanlar)
        $userProducts = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->pluck('order_items.product_id')
            ->toArray();

        if (empty($userProducts)) {
            return collect();
        }

        // En az 2 ortak ürün alan kullanıcılar
        $similarUsers = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('order_items.product_id', $userProducts)
            ->where('orders.user_id', '!=', $userId)
            ->groupBy('orders.user_id')
            ->havingRaw('COUNT(DISTINCT order_items.product_id) >= 2')
            ->pluck('orders.user_id')
            ->toArray();

        if (empty($similarUsers)) {
            return collect();
        }

        // Benzer kullanıcıların aldığı ama bizim kullanıcının almadığı ürünler
        return DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.user_id', $similarUsers)
            ->whereNotIn('order_items.product_id', $userProducts)
            ->where('products.is_active', true)
            ->where('products.stock', '>', 0)
            ->select('products.*', DB::raw('COUNT(*) as purchase_count'))
            ->groupBy('products.id')
            ->orderByDesc('purchase_count')
            ->limit(15)
            ->get()
            ->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'product' => $product,
                    'score' => min(100, 40 + ($product->purchase_count * 10)),
                ];
            });
    }

    /**
     * Görüntüleme geçmişine dayalı öneriler
     */
    private function getViewBasedRecommendations(int $userId): Collection
    {
        $recentViews = DB::table('product_views')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('created_at')
            ->limit(10)
            ->pluck('product_id')
            ->toArray();

        if (empty($recentViews)) {
            return collect();
        }

        // Görüntülenen ürünlerin kategorilerinden benzer ürünler
        $viewedCategories = DB::table('products')
            ->whereIn('id', $recentViews)
            ->pluck('category_id')
            ->unique()
            ->toArray();

        return DB::table('products')
            ->whereIn('category_id', $viewedCategories)
            ->whereNotIn('id', $recentViews)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderByDesc('rating')
            ->limit(15)
            ->get()
            ->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'product' => $product,
                    'score' => 50 + ($product->rating * 5),
                ];
            });
    }

    /**
     * Trend ürünler (kullanıcı profiline uygun)
     */
    private function getTrendingRecommendations(array $profile): Collection
    {
        $query = DB::table('products')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', now()->subDays(7))
            ->where('products.is_active', true)
            ->where('products.stock', '>', 0)
            ->select('products.*', DB::raw('COUNT(order_items.id) as weekly_sales'))
            ->groupBy('products.id')
            ->orderByDesc('weekly_sales');

        // Fiyat aralığı filtresi (profil varsa)
        if (!empty($profile['price_range']['avg'])) {
            $query->whereBetween('products.price', [
                $profile['price_range']['min'] * 0.5,
                $profile['price_range']['max'] * 2
            ]);
        }

        return $query->limit(15)->get()->map(function ($product) {
            return [
                'product_id' => $product->id,
                'product' => $product,
                'score' => min(100, 30 + ($product->weekly_sales * 2)),
            ];
        });
    }

    /**
     * Öneri kaynağından açıklama metni oluştur
     */
    private function generateReasonText(array $sources): string
    {
        $reasons = [];
        
        if (in_array('purchase', $sources)) {
            $reasons[] = 'Satın alma geçmişinize göre';
        }
        if (in_array('collaborative', $sources)) {
            $reasons[] = 'Sizin gibi alıcılar da aldı';
        }
        if (in_array('view_history', $sources)) {
            $reasons[] = 'Son görüntülediklerinize benzer';
        }
        if (in_array('trending', $sources)) {
            $reasons[] = 'Bu hafta trend';
        }

        return implode(' • ', $reasons);
    }

    /**
     * Profil özeti
     */
    private function getProfileSummary(array $profile): array
    {
        $topCategories = DB::table('categories')
            ->whereIn('id', array_keys($profile['category_preferences']))
            ->pluck('name', 'id')
            ->toArray();

        return [
            'favorite_categories' => array_values($topCategories),
            'price_preference' => $this->getPriceLabel($profile['price_range']['avg']),
            'activity_level' => $this->getActivityLevel($profile['total_orders']),
        ];
    }

    private function getPriceLabel(float $avg): string
    {
        if ($avg < 100) return 'Ekonomik';
        if ($avg < 500) return 'Orta Segment';
        if ($avg < 1500) return 'Premium';
        return 'Lüks';
    }

    private function getActivityLevel(int $orders): string
    {
        if ($orders < 2) return 'Yeni Müşteri';
        if ($orders < 10) return 'Aktif';
        if ($orders < 30) return 'Sadık Müşteri';
        return 'VIP';
    }

    /**
     * "Bunu Alanlar Şunları da Aldı" önerileri
     */
    public function getFrequentlyBoughtTogether(int $productId, int $limit = 5): array
    {
        $cacheKey = "ai.fbt.product.{$productId}";

        return Cache::remember($cacheKey, 3600, function () use ($productId, $limit) {
            // Bu ürünü içeren siparişlerdeki diğer ürünler
            $relatedProducts = DB::table('order_items as oi1')
                ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
                ->join('products', 'oi2.product_id', '=', 'products.id')
                ->where('oi1.product_id', $productId)
                ->where('oi2.product_id', '!=', $productId)
                ->where('products.is_active', true)
                ->where('products.stock', '>', 0)
                ->select('products.*', DB::raw('COUNT(*) as pair_count'))
                ->groupBy('products.id')
                ->orderByDesc('pair_count')
                ->limit($limit)
                ->get();

            return [
                'product_id' => $productId,
                'frequently_bought_together' => $relatedProducts->toArray(),
            ];
        });
    }

    /**
     * Benzer ürünler
     */
    public function getSimilarProducts(int $productId, int $limit = 8): array
    {
        $product = DB::table('products')->find($productId);
        if (!$product) {
            return ['similar_products' => []];
        }

        $similar = DB::table('products')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $productId)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->whereBetween('price', [$product->price * 0.5, $product->price * 1.5])
            ->orderByDesc('rating')
            ->limit($limit)
            ->get();

        return [
            'product_id' => $productId,
            'similar_products' => $similar->toArray(),
        ];
    }

    /**
     * Cache temizle
     */
    public function clearUserCache(int $userId): void
    {
        Cache::forget("ai.recommendations.user.{$userId}");
    }
}
