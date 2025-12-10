<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserBehaviorService
{
    /**
     * Ürün görüntüleme kaydı
     */
    public function trackProductView(int $productId, ?string $source = null, ?string $referrer = null): void
    {
        DB::table('product_views')->insert([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'session_id' => session()->getId(),
            'source' => $source,
            'referrer' => $referrer,
            'device_type' => $this->getDeviceType(),
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Ürün görüntüleme süresini güncelle
     */
    public function updateViewDuration(int $productId, int $durationSeconds): void
    {
        DB::table('product_views')
            ->where('product_id', $productId)
            ->where('session_id', session()->getId())
            ->where('created_at', '>=', now()->subMinutes(30))
            ->orderByDesc('id')
            ->limit(1)
            ->update(['duration_seconds' => $durationSeconds]);
    }

    /**
     * Kampanya görüntüleme kaydı
     */
    public function trackCampaignView(int $campaignId, ?string $placement = null): void
    {
        // Aynı oturumda aynı kampanyayı tekrar kaydetme (spam önleme)
        $exists = DB::table('campaign_views')
            ->where('campaign_id', $campaignId)
            ->where('session_id', session()->getId())
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if (!$exists) {
            DB::table('campaign_views')->insert([
                'user_id' => Auth::id(),
                'campaign_id' => $campaignId,
                'session_id' => session()->getId(),
                'placement' => $placement,
                'device_type' => $this->getDeviceType(),
                'ip_address' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Kampanya tıklama kaydı
     */
    public function trackCampaignClick(int $campaignId, ?string $clickTarget = null): void
    {
        DB::table('campaign_clicks')->insert([
            'user_id' => Auth::id(),
            'campaign_id' => $campaignId,
            'session_id' => session()->getId(),
            'click_target' => $clickTarget,
            'device_type' => $this->getDeviceType(),
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Arama kaydı
     */
    public function trackSearch(string $query, int $resultsCount): int
    {
        return DB::table('search_history')->insertGetId([
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'query' => Str::limit($query, 255),
            'results_count' => $resultsCount,
            'device_type' => $this->getDeviceType(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Arama sonucuna tıklama kaydı
     */
    public function trackSearchClick(int $searchId, int $productId): void
    {
        DB::table('search_history')
            ->where('id', $searchId)
            ->update([
                'clicked_result' => true,
                'clicked_product_id' => $productId,
            ]);
    }

    /**
     * Sepet olayı kaydı
     */
    public function trackCartEvent(int $productId, string $eventType, int $quantity, float $price, ?string $source = null): void
    {
        DB::table('cart_events')->insert([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'session_id' => session()->getId(),
            'event_type' => $eventType,
            'quantity' => $quantity,
            'price_at_event' => $price,
            'source' => $source,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Cihaz tipini algıla
     */
    private function getDeviceType(): string
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/mobile|android|iphone|ipad|ipod/i', $userAgent)) {
            if (preg_match('/ipad|tablet/i', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        
        return 'desktop';
    }

    /**
     * Kullanıcının son görüntülemelerini al
     */
    public function getRecentViews(?int $userId = null, int $limit = 10): array
    {
        $query = DB::table('product_views')
            ->join('products', 'product_views.product_id', '=', 'products.id')
            ->select('products.*', 'product_views.created_at as viewed_at')
            ->where('products.is_active', true)
            ->orderByDesc('product_views.created_at')
            ->limit($limit);

        if ($userId) {
            $query->where('product_views.user_id', $userId);
        } else {
            $query->where('product_views.session_id', session()->getId());
        }

        return $query->get()->toArray();
    }

    /**
     * Popüler aramalar
     */
    public function getPopularSearches(int $limit = 10): array
    {
        return DB::table('search_history')
            ->select('query', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Trend ürünler (görüntüleme bazlı)
     */
    public function getTrendingProducts(int $limit = 10): array
    {
        return DB::table('product_views')
            ->join('products', 'product_views.product_id', '=', 'products.id')
            ->select('products.*', DB::raw('COUNT(product_views.id) as view_count'))
            ->where('product_views.created_at', '>=', now()->subDays(7))
            ->where('products.is_active', true)
            ->where('products.stock', '>', 0)
            ->groupBy('products.id')
            ->orderByDesc('view_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Terk edilen sepet analizi
     */
    public function getAbandonedCartItems(?int $userId = null): array
    {
        $query = DB::table('cart_events')
            ->join('products', 'cart_events.product_id', '=', 'products.id')
            ->select(
                'products.*',
                'cart_events.quantity',
                'cart_events.price_at_event',
                'cart_events.created_at as added_at'
            )
            ->where('cart_events.event_type', 'add')
            ->where('cart_events.created_at', '>=', now()->subDays(7))
            ->where('products.is_active', true);

        if ($userId) {
            $query->where('cart_events.user_id', $userId);
        }

        // Satın alınmamış ürünler
        $query->whereNotExists(function ($q) use ($userId) {
            $q->select(DB::raw(1))
                ->from('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereColumn('order_items.product_id', 'cart_events.product_id')
                ->where('orders.created_at', '>=', DB::raw('cart_events.created_at'));

            if ($userId) {
                $q->where('orders.user_id', $userId);
            }
        });

        return $query->get()->toArray();
    }
}
