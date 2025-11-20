<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardModuleService
{
    /**
     * Get all active dashboard modules
     */
    public function getActiveModules(string $userType = 'admin'): array
    {
        return Cache::remember("dashboard_modules_{$userType}", 3600, function () use ($userType) {
            $modules = DB::table('dashboard_modules')
                ->where('user_type', $userType)
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get();

            return $modules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'name' => $module->name,
                    'component' => $module->component,
                    'icon' => $module->icon,
                    'type' => $module->type, // metric, chart, list, card
                    'size' => $module->size, // sm, md, lg, xl
                    'config' => json_decode($module->config, true),
                    'permissions' => json_decode($module->permissions, true),
                ];
            })->toArray();
        });
    }

    /**
     * Get module data dynamically
     */
    public function getModuleData(string $moduleName, array $filters = []): array
    {
        return match($moduleName) {
            'orders_today' => $this->getOrdersToday($filters),
            'revenue_metrics' => $this->getRevenueMetrics($filters),
            'active_campaigns' => $this->getActiveCampaigns($filters),
            'seller_performance' => $this->getSellerPerformance($filters),
            'product_analytics' => $this->getProductAnalytics($filters),
            'customer_segments' => $this->getCustomerSegments($filters),
            default => []
        };
    }

    private function getOrdersToday(array $filters): array
    {
        $today = now()->startOfDay();
        
        $orders = DB::table('orders')
            ->where('created_at', '>=', $today)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "shipped" THEN 1 ELSE 0 END) as shipped,
                SUM(total_amount) as revenue
            ')
            ->first();

        return [
            'total' => $orders->total ?? 0,
            'completed' => $orders->completed ?? 0,
            'pending' => $orders->pending ?? 0,
            'shipped' => $orders->shipped ?? 0,
            'revenue' => $orders->revenue ?? 0,
            'avg_order_value' => $orders->total > 0 ? ($orders->revenue / $orders->total) : 0,
        ];
    }

    private function getRevenueMetrics(array $filters): array
    {
        $period = $filters['period'] ?? 'today';
        $startDate = match($period) {
            'today' => now()->startOfDay(),
            'week' => now()->subDays(7),
            'month' => now()->subDays(30),
            default => now()->startOfDay()
        };

        $revenue = DB::table('orders')
            ->where('created_at', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('
                SUM(total_amount) as total_revenue,
                SUM(total_amount - shipping_cost) as net_revenue,
                AVG(total_amount) as avg_order_value,
                COUNT(*) as order_count
            ')
            ->first();

        return [
            'total_revenue' => $revenue->total_revenue ?? 0,
            'net_revenue' => $revenue->net_revenue ?? 0,
            'avg_order_value' => $revenue->avg_order_value ?? 0,
            'order_count' => $revenue->order_count ?? 0,
            'period' => $period,
        ];
    }

    private function getActiveCampaigns(array $filters): array
    {
        $campaigns = DB::table('incentive_campaigns')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->select('id', 'name', 'type', 'discount_amount', 'usage_count', 'usage_limit')
            ->get();

        return $campaigns->map(function ($campaign) {
            $utilizationRate = $campaign->usage_limit > 0 
                ? ($campaign->usage_count / $campaign->usage_limit) * 100 
                : 0;

            return [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'type' => $campaign->type,
                'discount' => $campaign->discount_amount,
                'usage' => $campaign->usage_count,
                'limit' => $campaign->usage_limit,
                'utilization' => round($utilizationRate, 1),
                'status' => $utilizationRate >= 90 ? 'critical' : ($utilizationRate >= 70 ? 'warning' : 'normal'),
            ];
        })->toArray();
    }

    private function getSellerPerformance(array $filters): array
    {
        $limit = $filters['limit'] ?? 10;

        $sellers = DB::table('users')
            ->where('role', 'seller')
            ->leftJoin('products', 'users.id', '=', 'products.vendor_id')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->selectRaw('
                users.id,
                users.name,
                COUNT(DISTINCT products.id) as product_count,
                COUNT(DISTINCT order_details.id) as sales_count,
                SUM(order_details.price * order_details.quantity) as total_revenue
            ')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        return $sellers->map(function ($seller, $index) {
            return [
                'rank' => $index + 1,
                'id' => $seller->id,
                'name' => $seller->name,
                'products' => $seller->product_count ?? 0,
                'sales' => $seller->sales_count ?? 0,
                'revenue' => $seller->total_revenue ?? 0,
                'avg_sale' => $seller->sales_count > 0 ? ($seller->total_revenue / $seller->sales_count) : 0,
            ];
        })->toArray();
    }

    private function getProductAnalytics(array $filters): array
    {
        $topProducts = DB::table('products')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->selectRaw('
                products.id,
                products.name,
                products.price,
                products.stock,
                COUNT(order_details.id) as order_count,
                SUM(order_details.quantity) as total_sold,
                SUM(order_details.price * order_details.quantity) as revenue
            ')
            ->groupBy('products.id', 'products.name', 'products.price', 'products.stock')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return [
            'top_products' => $topProducts->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'orders' => $product->order_count ?? 0,
                    'sold' => $product->total_sold ?? 0,
                    'revenue' => $product->revenue ?? 0,
                    'stock_status' => $product->stock < 10 ? 'low' : 'normal',
                ];
            })->toArray(),
        ];
    }

    private function getCustomerSegments(array $filters): array
    {
        // High value customers
        $highValue = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->selectRaw('COUNT(DISTINCT users.id) as count')
            ->havingRaw('SUM(orders.total_amount) > 5000')
            ->value('count') ?? 0;

        // Active customers (last 30 days)
        $active = DB::table('orders')
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('user_id')
            ->count();

        // At risk customers (60-120 days no order)
        $atRisk = DB::table('users')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('orders')
                    ->whereColumn('orders.user_id', 'users.id')
                    ->where('orders.created_at', '<=', now()->subDays(60))
                    ->where('orders.created_at', '>=', now()->subDays(120));
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('orders')
                    ->whereColumn('orders.user_id', 'users.id')
                    ->where('orders.created_at', '>', now()->subDays(60));
            })
            ->count();

        return [
            'high_value' => $highValue,
            'active' => $active,
            'at_risk' => $atRisk,
            'total' => DB::table('users')->where('role', 'buyer')->count(),
        ];
    }

    /**
     * Update module configuration
     */
    public function updateModuleConfig(int $moduleId, array $config): bool
    {
        DB::table('dashboard_modules')
            ->where('id', $moduleId)
            ->update([
                'config' => json_encode($config),
                'updated_at' => now(),
            ]);

        Cache::forget('dashboard_modules_admin');
        Cache::forget('dashboard_modules_seller');
        Cache::forget('dashboard_modules_buyer');

        return true;
    }
}
