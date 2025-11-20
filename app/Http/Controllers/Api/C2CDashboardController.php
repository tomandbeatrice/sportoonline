<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * C2C Marketplace Dashboard Controller
 * Handles seller, buyer, and admin dashboard data for C2C marketplace
 */
class C2CDashboardController extends Controller
{
    /**
     * Get dashboard data based on user role
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role; // 'seller', 'buyer', or 'admin'

        switch ($role) {
            case 'seller':
                return $this->getSellerDashboard($user);
            case 'buyer':
                return $this->getBuyerDashboard($user);
            case 'admin':
                return $this->getAdminDashboard($user);
            default:
                return response()->json(['error' => 'Invalid role'], 403);
        }
    }

    /**
     * Seller Dashboard Data
     */
    private function getSellerDashboard($user)
    {
        $sellerId = $user->id;

        // Stats
        $stats = [
            'total_earnings' => DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.seller_id', $sellerId)
                ->where('orders.status', 'completed')
                ->sum('order_items.subtotal'),
            
            'active_products' => DB::table('products')
                ->where('seller_id', $sellerId)
                ->where('status', 'active')
                ->count(),
            
            'pending_orders' => DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.seller_id', $sellerId)
                ->whereIn('orders.status', ['pending', 'processing'])
                ->distinct('orders.id')
                ->count(),
            
            'store_rating' => DB::table('reviews')
                ->join('products', 'reviews.product_id', '=', 'products.id')
                ->where('products.seller_id', $sellerId)
                ->avg('reviews.rating') ?? 0
        ];

        // Top performing products
        $products = DB::table('products')
            ->select([
                'products.id',
                'products.name',
                'products.image',
                'products.stock',
                DB::raw('COUNT(order_items.id) as sales'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            ])
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->where('products.seller_id', $sellerId)
            ->groupBy('products.id', 'products.name', 'products.image', 'products.stock')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        // Pending orders
        $orders = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('products.seller_id', $sellerId)
            ->whereIn('orders.status', ['pending', 'processing'])
            ->select([
                'orders.id',
                'orders.status',
                'orders.total_amount',
                'orders.created_at',
                'products.name as product_name',
                'users.name as buyer_name'
            ])
            ->orderBy('orders.created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent activities
        $activities = $this->getSellerActivities($sellerId);

        return response()->json([
            'role' => 'seller',
            'stats' => $stats,
            'products' => $products,
            'orders' => $orders,
            'activities' => $activities
        ]);
    }

    /**
     * Buyer Dashboard Data
     */
    private function getBuyerDashboard($user)
    {
        $buyerId = $user->id;

        // Stats
        $stats = [
            'total_spent' => DB::table('orders')
                ->where('user_id', $buyerId)
                ->where('status', 'completed')
                ->sum('total_amount'),
            
            'active_orders' => DB::table('orders')
                ->where('user_id', $buyerId)
                ->whereIn('status', ['pending', 'processing', 'shipped'])
                ->count(),
            
            'favorites' => DB::table('favorites')
                ->where('user_id', $buyerId)
                ->count(),
            
            'loyalty_points' => DB::table('users')
                ->where('id', $buyerId)
                ->value('loyalty_points') ?? 0
        ];

        // Active orders
        $orders = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('users as sellers', 'products.seller_id', '=', 'sellers.id')
            ->where('orders.user_id', $buyerId)
            ->select([
                'orders.id',
                'orders.status',
                'orders.total_amount',
                'orders.created_at',
                'products.name as product_name',
                'products.image as product_image',
                'sellers.name as seller_name'
            ])
            ->orderBy('orders.created_at', 'desc')
            ->limit(10)
            ->get();

        // Personalized recommendations
        $recommendations = DB::table('products')
            ->join('users as sellers', 'products.seller_id', '=', 'sellers.id')
            ->where('products.status', 'active')
            ->where('products.stock', '>', 0)
            ->select([
                'products.id',
                'products.name',
                'products.image',
                'products.price',
                'sellers.name as seller_name'
            ])
            ->inRandomOrder()
            ->limit(6)
            ->get();

        // Recent activities
        $activities = $this->getBuyerActivities($buyerId);

        return response()->json([
            'role' => 'buyer',
            'stats' => $stats,
            'orders' => $orders,
            'recommendations' => $recommendations,
            'activities' => $activities
        ]);
    }

    /**
     * Admin Dashboard Data
     */
    private function getAdminDashboard($user)
    {
        // Platform-wide stats
        $stats = [
            'platform_revenue' => DB::table('orders')
                ->where('status', 'completed')
                ->sum('commission_amount'), // Assuming commission column exists
            
            'total_sellers' => DB::table('users')
                ->where('role', 'seller')
                ->where('status', 'active')
                ->count(),
            
            'daily_transactions' => DB::table('orders')
                ->whereDate('created_at', today())
                ->count(),
            
            'active_campaigns' => DB::table('campaigns')
                ->where('status', 'active')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count()
        ];

        // Platform metrics
        $metrics = [
            [
                'label' => 'Satıcı Aktivitesi',
                'value' => $this->calculateSellerActivity(),
                'percentage' => '87%'
            ],
            [
                'label' => 'Alıcı Memnuniyeti',
                'value' => $this->calculateBuyerSatisfaction(),
                'percentage' => '92%'
            ],
            [
                'label' => 'İşlem Başarı Oranı',
                'value' => $this->calculateTransactionSuccessRate(),
                'percentage' => '94%'
            ]
        ];

        // Pending approvals
        $approvals = [
            'sellers' => DB::table('seller_applications')
                ->where('status', 'pending')
                ->count(),
            'products' => DB::table('products')
                ->where('approval_status', 'pending')
                ->count(),
            'refunds' => DB::table('refund_requests')
                ->where('status', 'pending')
                ->count()
        ];

        // Active disputes
        $disputes = DB::table('disputes')
            ->join('orders', 'disputes.order_id', '=', 'orders.id')
            ->join('users as buyers', 'orders.user_id', '=', 'buyers.id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('users as sellers', 'products.seller_id', '=', 'sellers.id')
            ->where('disputes.status', 'open')
            ->select([
                'disputes.id',
                'disputes.issue',
                'disputes.created_at',
                'buyers.name as buyer_name',
                'sellers.name as seller_name'
            ])
            ->orderBy('disputes.created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent platform activities
        $activities = $this->getAdminActivities();

        return response()->json([
            'role' => 'admin',
            'stats' => $stats,
            'metrics' => $metrics,
            'approvals' => $approvals,
            'disputes' => $disputes,
            'activities' => $activities
        ]);
    }

    /**
     * Get seller activities
     */
    private function getSellerActivities($sellerId)
    {
        $activities = [];

        // New orders
        $newOrders = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $sellerId)
            ->where('orders.created_at', '>=', now()->subHours(24))
            ->select('orders.id', 'orders.created_at')
            ->get();

        foreach ($newOrders as $order) {
            $activities[] = [
                'icon' => '🛒',
                'title' => 'Yeni Sipariş',
                'description' => "Sipariş #$order->id alındı",
                'time' => $order->created_at->diffForHumans()
            ];
        }

        // New reviews
        $newReviews = DB::table('reviews')
            ->join('products', 'reviews.product_id', '=', 'products.id')
            ->where('products.seller_id', $sellerId)
            ->where('reviews.created_at', '>=', now()->subHours(24))
            ->select('reviews.rating', 'reviews.created_at', 'products.name')
            ->get();

        foreach ($newReviews as $review) {
            $activities[] = [
                'icon' => '⭐',
                'title' => 'Yeni Değerlendirme',
                'description' => "$review->name için $review->rating yıldız",
                'time' => $review->created_at->diffForHumans()
            ];
        }

        return array_slice($activities, 0, 10);
    }

    /**
     * Get buyer activities
     */
    private function getBuyerActivities($buyerId)
    {
        $activities = [];

        // Recent orders
        $recentOrders = DB::table('orders')
            ->where('user_id', $buyerId)
            ->where('created_at', '>=', now()->subDays(7))
            ->select('id', 'status', 'created_at')
            ->get();

        foreach ($recentOrders as $order) {
            $activities[] = [
                'icon' => '📦',
                'title' => 'Sipariş Durumu',
                'description' => "Sipariş #$order->id - $order->status",
                'time' => $order->created_at->diffForHumans()
            ];
        }

        // Recent favorites
        $recentFavorites = DB::table('favorites')
            ->join('products', 'favorites.product_id', '=', 'products.id')
            ->where('favorites.user_id', $buyerId)
            ->where('favorites.created_at', '>=', now()->subDays(7))
            ->select('products.name', 'favorites.created_at')
            ->get();

        foreach ($recentFavorites as $favorite) {
            $activities[] = [
                'icon' => '❤️',
                'title' => 'Favorilere Eklendi',
                'description' => $favorite->name,
                'time' => $favorite->created_at->diffForHumans()
            ];
        }

        return array_slice($activities, 0, 10);
    }

    /**
     * Get admin activities
     */
    private function getAdminActivities()
    {
        $activities = [];

        // New seller applications
        $newSellers = DB::table('seller_applications')
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subDays(1))
            ->count();

        if ($newSellers > 0) {
            $activities[] = [
                'icon' => '👥',
                'title' => 'Yeni Satıcı Başvuruları',
                'description' => "$newSellers yeni başvuru bekliyor",
                'time' => 'Bugün'
            ];
        }

        // Active disputes
        $disputes = DB::table('disputes')
            ->where('status', 'open')
            ->count();

        if ($disputes > 0) {
            $activities[] = [
                'icon' => '⚖️',
                'title' => 'Aktif Anlaşmazlıklar',
                'description' => "$disputes anlaşmazlık çözüm bekliyor",
                'time' => 'Devam ediyor'
            ];
        }

        return $activities;
    }

    /**
     * Calculate seller activity rate
     */
    private function calculateSellerActivity()
    {
        $totalSellers = DB::table('users')->where('role', 'seller')->count();
        $activeSellers = DB::table('users')
            ->where('role', 'seller')
            ->where('last_activity_at', '>=', now()->subDays(7))
            ->count();

        return $totalSellers > 0 ? round(($activeSellers / $totalSellers) * 100) . '%' : '0%';
    }

    /**
     * Calculate buyer satisfaction
     */
    private function calculateBuyerSatisfaction()
    {
        $avgRating = DB::table('reviews')->avg('rating') ?? 0;
        return round($avgRating, 1) . '/5';
    }

    /**
     * Calculate transaction success rate
     */
    private function calculateTransactionSuccessRate()
    {
        $total = DB::table('orders')->count();
        $successful = DB::table('orders')->where('status', 'completed')->count();

        return $total > 0 ? round(($successful / $total) * 100) . '%' : '0%';
    }

    /**
     * Execute workflow
     */
    public function executeWorkflow(Request $request)
    {
        $workflowId = $request->input('workflow_id');
        $user = Auth::user();

        // Workflow logic based on ID and user role
        // This would trigger background jobs, notifications, etc.

        return response()->json([
            'message' => "Workflow $workflowId initiated",
            'status' => 'processing'
        ]);
    }

    /**
     * Quick action handler
     */
    public function quickAction(Request $request)
    {
        $actionId = $request->input('action_id');
        $user = Auth::user();

        // Handle quick actions based on ID and user role
        switch ($actionId) {
            case 'add-product':
                return response()->json(['redirect' => '/seller/products/add']);
            case 'process-orders':
                return response()->json(['redirect' => '/seller/orders']);
            case 'browse':
                return response()->json(['redirect' => '/marketplace']);
            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }
    }
}
