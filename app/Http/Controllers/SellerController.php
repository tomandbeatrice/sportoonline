<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Seller;
use App\Models\User;
use App\Models\CampaignPlan;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Comment;
use App\Helpers\ExportLogHelper;
use App\Services\CampaignService;

class SellerController extends Controller
{
    // Satıcı dashboard istatistikleri
    public function stats(Request $request)
    {
        $seller = $this->ensureSellerContext($request);

        $productCount = $seller->products()->count();

        $orderItemsQuery = OrderItem::whereHas('product', fn($q) => $q->where('seller_id', $seller->id));

        $totalOrders = (clone $orderItemsQuery)
            ->distinct('order_id')
            ->count('order_id');

        $totalRevenue = (clone $orderItemsQuery)
            ->select(DB::raw('SUM(quantity * price) as revenue'))
            ->value('revenue') ?? 0;

        $avgRating = Review::whereHas('product', fn($q) => $q->where('seller_id', $seller->id))
            ->avg('rating');

        return response()->json([
            'total_products' => $productCount,
            'total_orders' => $totalOrders,
            'total_revenue' => round($totalRevenue, 2),
            'avg_rating' => $avgRating ? round($avgRating, 2) : null,
        ]);
    }

    // Satıcının ürünlerini listele
    public function products(Request $request)
    {
        $seller = $this->ensureSellerContext($request);

        $products = $seller->products()
            ->with('category')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json($products);
    }

    // Satıcının siparişlerini listele
    public function orders(Request $request)
    {
        $seller = $this->ensureSellerContext($request);

        // Get all orders that contain at least one product from the current seller
        $orders = Order::whereHas('items.product', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })
        ->with([
            // Eager load the user who placed the order
            'user:id,name', 
            // Eager load only the order items that belong to the current seller
            'items' => function ($query) use ($seller) {
                $query->whereHas('product', function ($subQuery) use ($seller) {
                    $subQuery->where('seller_id', $seller->id);
                })
                // Also load the product details for each of those items
                ->with('product:id,name,image_url'); 
            }
        ])
        ->orderByDesc('created_at')
        ->get();

        // We don't need to map here anymore as we will use accessors in the model
        // to provide translated statuses and other computed properties.
        return response()->json($orders);
    }

    // Satıcının finansal raporlarını getir
    public function financialReport(Request $request)
    {
        $seller = $this->ensureSellerContext($request);

        // Get all order items for this seller's products with their financial data
        $orderItems = OrderItem::whereHas('product', fn($q) => $q->where('seller_id', $seller->id))
            ->with(['order:id,status,created_at,transaction_id', 'product:id,name'])
            ->orderByDesc('created_at')
            ->get();

        // Calculate totals
        $totalRevenue = $orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $totalPlatformFees = $orderItems->sum('platform_fee');
        $totalSellerPayout = $orderItems->sum('seller_payout');

        // Group by status to show pending vs paid earnings
        $pendingPayout = $orderItems->filter(fn($item) => $item->order->status !== 'paid')
            ->sum('seller_payout');

        $confirmedPayout = $orderItems->filter(fn($item) => $item->order->status === 'paid')
            ->sum('seller_payout');

        return response()->json([
            'summary' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_platform_fees' => round($totalPlatformFees, 2),
                'total_seller_payout' => round($totalSellerPayout, 2),
                'pending_payout' => round($pendingPayout, 2),
                'confirmed_payout' => round($confirmedPayout, 2),
            ],
            'transactions' => $orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'order_id' => $item->order_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price_per_unit' => $item->price,
                    'total' => round($item->quantity * $item->price, 2),
                    'platform_fee' => round($item->platform_fee, 2),
                    'seller_payout' => round($item->seller_payout, 2),
                    'order_status' => $item->order->status,
                    'transaction_id' => $item->order->transaction_id,
                    'date' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    public function sellerBehaviorScoreSummary()
    {
        return Seller::withCount([
            'products as update_count' => fn($q) => $q->where('updated_at', '>', now()->subDays(7)),
            'campaignRestarts as restart_count',
            'exports as export_usage'
        ])
        ->select('id', 'score', 'response_time', 'comment_response_rate')
        ->orderByDesc('score')
        ->take(50)
        ->get();
    }

    public function generateCampaignPlan($id)
    {
        $seller = Seller::select('id', 'score', 'response_time', 'comment_response_rate')->findOrFail($id);

        $rules = Cache::get('suggestion_rules', [
            'discount_threshold' => 20,
            'product_count_threshold' => 10,
            'campaign_types' => ['Outlet Boost', 'Flash Sale']
        ]);

        $type = $seller->score > 12 ? $rules['campaign_types'][0] : $rules['campaign_types'][1];
        $suggested_discount = $seller->comment_response_rate > 60 ? 25 : 15;
        $duration = $seller->response_time < 30 ? 7 : 3;

        return [
            'seller_id' => $seller->id,
            'campaign_type' => $type,
            'suggested_discount' => $suggested_discount,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays($duration)->toDateString(),
            'reasoning' => "Skor: {$seller->score}, Yanıt süresi: {$seller->response_time}, Yorum yanıt oranı: {$seller->comment_response_rate}"
        ];
    }

    public function approveCampaignPlan(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $plan = CampaignPlan::updateOrCreate(
            ['seller_id' => $id],
            ['status' => $validated['status'], 'approved_at' => now()]
        );

        return response()->json([
            'status' => $plan->status,
            'message' => $plan->status === 'approved'
                ? 'Kampanya planı onaylandı ✅'
                : 'Kampanya planı iptal edildi ❌'
        ]);
    }

    public function deployCampaignPlan($id)
    {
        $plan = CampaignPlan::where('seller_id', $id)
            ->where('status', 'approved')
            ->firstOrFail();

        Product::where('seller_id', $id)->update([
            'campaign_tag' => $plan->campaign_type,
            'discount_rate' => $plan->suggested_discount,
            'campaign_start' => $plan->start_date,
            'campaign_end' => $plan->end_date
        ]);

        ExportLogHelper::log('Kampanya canlıya alındı', [
            'seller_id' => $id,
            'campaign_type' => $plan->campaign_type,
            'discount' => $plan->suggested_discount,
            'start' => $plan->start_date,
            'end' => $plan->end_date
        ]);

        return response()->json(['status' => 'deployed', 'message' => 'Kampanya canlıya alındı ✅']);
    }

    public function replanBasedOnFeedback(Request $request, $id)
    {
        $validated = $request->validate(['feedback' => 'required|string']);
        $seller = Seller::select('id', 'score', 'response_time', 'comment_response_rate')->findOrFail($id);

        $positive = str_contains(strtolower($validated['feedback']), 'evet') || str_contains($validated['feedback'], '👍');
        $suggested_discount = $positive ? 30 : 15;
        $duration = $seller->response_time < 20 ? 5 : 3;
        $type = $seller->score > 10 ? 'Outlet Boost' : 'Flash Sale';

        CampaignPlan::updateOrCreate(
            ['seller_id' => $id],
            [
                'campaign_type' => $type,
                'suggested_discount' => $suggested_discount,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays($duration)->toDateString(),
                'status' => 'pending',
                'feedback' => $validated['feedback']
            ]
        );

        return response()->json([
            'status' => 'replanned',
            'message' => "Yeni plan oluşturuldu: {$type} ile %{$suggested_discount} indirim / Süre: {$duration} gün"
        ]);
    }

    public function campaignTimeline($id)
    {
        $plans = CampaignPlan::where('seller_id', $id)
            ->orderByDesc('created_at')
            ->get();

        return $plans->map(fn($plan) => [
            'timestamp' => $plan->created_at->toDateTimeString(),
            'event' => match ($plan->status) {
                'approved' => 'Plan onaylandı',
                'rejected' => 'Plan reddedildi',
                'pending' => 'Yeni öneri üretildi',
                default => 'Durum bilinmiyor'
            },
            'type' => $plan->campaign_type,
            'discount' => $plan->suggested_discount,
            'feedback' => $plan->feedback,
            'start' => $plan->start_date,
            'end' => $plan->end_date
        ]);
    }

    public function campaignSegmentTrend($id)
    {
        $plans = CampaignPlan::where('seller_id', $id)->orderBy('created_at')->get();
        $score = Seller::find($id)->score;

        return $plans->map(fn($plan) => [
            'date' => $plan->created_at->format('Y-m-d'),
            'type' => $plan->campaign_type,
            'discount' => $plan->suggested_discount,
            'status' => $plan->status,
            'score' => $score
        ]);
    }

    public function campaignRegressionAnalysis($id)
    {
        $seller = Seller::select('score', 'response_time', 'comment_response_rate')->findOrFail($id);
        $updateCount = $seller->products()->where('updated_at', '>', now()->subDays(7))->count();
        $exportUsage = $seller->exports()->count();

        $x = [
            'response_time' => $seller->response_time,
            'comment_response_rate' => $seller->comment_response_rate,
            'update_count' => $updateCount,
            'export_usage' => $exportUsage
        ];

        $coefficients = Config::get('campaign.regression_coefficients', [
            'response_time' => -0.3,
            'comment_response_rate' => 0.5,
            'update_count' => 0.2,
            'export_usage' => 0.1
        ]);

        $predictedScore = round(array_sum(array_map(fn($k) => $x[$k] * $coefficients[$k], array_keys($x))), 2);

        return [
            'actual_score' => $seller->score,
            'predicted_score' => $predictedScore,
            'coefficients' => $coefficients,
            'inputs' => $x
        ];
    }

    public function campaignScatterData()
    {
        $sellers = Seller::select('id', 'score', 'comment_response_rate')->get();
        $plans = CampaignPlan::whereIn('seller_id', $sellers->pluck('id'))->get();

        return $plans->map(fn($plan) => [
            'x' => $sellers->firstWhere('id', $plan->seller_id)?->comment_response_rate,
            'y' => $sellers->firstWhere('id', $plan->seller_id)?->score,
            'type' => $plan->campaign_type,
            'seller_id' => $plan->seller_id
        ]);
    }

       public function predictCampaignSuccess($id, CampaignService $service)
    {
        $seller = Seller::select('score', 'response_time', 'comment_response_rate')->findOrFail($id);

        $updateCount = $seller->products()
            ->where('updated_at', '>', now()->subDays(7))
            ->count();

        $exportUsage = $seller->exports()->count();

        $inputs = [
            'response_time' => $seller->response_time,
            'comment_response_rate' => $seller->comment_response_rate,
            'update_count' => $updateCount,
            'export_usage' => $exportUsage
        ];

        $score = $service->calculateScore($inputs);
        $suggestion = $service->suggestCampaign($score, $inputs['comment_response_rate']);

        return [
            'predicted_score' => $score,
            'suggested_campaign' => $suggestion['type'],
            'suggested_discount' => $suggestion['discount'],
            'inputs' => $inputs
        ];
    }

    public function suggestSegmentedCampaign($id, CampaignService $service)
    {
        $seller = Seller::select('score', 'response_time', 'comment_response_rate')->findOrFail($id);

        $updateCount = $seller->products()
            ->where('updated_at', '>', now()->subDays(7))
            ->count();

        $exportUsage = $seller->exports()->count();

        $inputs = [
            'response_time' => $seller->response_time,
            'comment_response_rate' => $seller->comment_response_rate,
            'update_count' => $updateCount,
            'export_usage' => $exportUsage
        ];

        return $service->suggestSegmentedCampaign($inputs);
    }

    public function segmentHeatmapData(CampaignService $service)
    {
    $sellers = Seller::select('id', 'score', 'response_time', 'comment_response_rate')->get();

    $data = [];

    foreach ($sellers as $seller) {
        $updateCount = $seller->products()->where('updated_at', '>', now()->subDays(7))->count();
        $exportUsage = $seller->exports()->count();

        $inputs = [
            'response_time' => $seller->response_time,
            'comment_response_rate' => $seller->comment_response_rate,
            'update_count' => $updateCount,
            'export_usage' => $exportUsage
        ];

        $suggestion = $service->suggestSegmentedCampaign($inputs);

        $data[] = [
            'seller_id' => $seller->id,
            'score' => $suggestion['score'],
            'segment' => $suggestion['segment'],
            'campaign_type' => $suggestion['campaign_type']
        ];
    }

    return $data;
}
public function segmentLearningSummary(CampaignService $service)
{
    $plans = CampaignPlan::whereNotNull('feedback')
        ->whereNotNull('approved_at')
        ->get(['seller_id', 'campaign_type', 'feedback', 'created_at']);

    $history = [];

    foreach ($plans as $plan) {
        $seller = Seller::find($plan->seller_id);
        $preScore = $seller->score; // örnek: plan öncesi skor
        $postScore = $seller->score + rand(-3, 5); // simülasyon: plan sonrası skor

        $segment = match ($plan->campaign_type) {
            'Outlet Boost' => 'Premium',
            'Flash Sale' => 'Standard',
            default => 'Low'
        };

        $history[] = [
            'segment' => $segment,
            'pre_score' => $preScore,
            'post_score' => $postScore
        ];
    }

    return $service->learnFromSegmentHistory($history);
    }

    public function refreshCampaignStrategy(CampaignService $service)
{
    $plans = CampaignPlan::whereNotNull('feedback')
        ->whereNotNull('approved_at')
        ->get(['seller_id', 'campaign_type', 'feedback']);

    $history = [];

    foreach ($plans as $plan) {
        $seller = Seller::find($plan->seller_id);
        $preScore = $seller->score;
        $postScore = $seller->score + rand(-2, 4); // simülasyon

        $segment = match ($plan->campaign_type) {
            'Outlet Boost' => 'Premium',
            'Flash Sale' => 'Standard',
            default => 'Low'
        };

        $history[] = [
            'segment' => $segment,
            'pre_score' => $preScore,
            'post_score' => $postScore
        ];
    }

    $learned = $service->learnFromSegmentHistory($history);
    $service->updateStrategyFromLearning($learned);

    return response()->json([
        'status' => 'updated',
        'weights' => $learned
    ]);
    }

    public function strategyAccuracy(CampaignService $service)
    {
    $plans = CampaignPlan::whereNotNull('approved_at')->get();
    $correct = 0;
    $total = 0;

    foreach ($plans as $plan) {
        $seller = Seller::find($plan->seller_id);
        $updateCount = $seller->products()->where('updated_at', '>', now()->subDays(7))->count();
        $exportUsage = $seller->exports()->count();

        $inputs = [
            'response_time' => $seller->response_time,
            'comment_response_rate' => $seller->comment_response_rate,
            'update_count' => $updateCount,
            'export_usage' => $exportUsage
        ];

        $suggestion = $service->suggestSegmentedCampaign($inputs);
        $expectedSegment = $suggestion['segment'];

        $actualSegment = match ($plan->campaign_type) {
            'Outlet Boost' => 'Premium',
            'Flash Sale' => 'Standard',
            default => 'Low'
        };

        if ($expectedSegment === $actualSegment) {
            $correct++;
        }

        $total++;
    }

    $accuracy = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

    return response()->json([
        'total' => $total,
        'correct' => $correct,
        'accuracy_percent' => $accuracy
    ]);
}
public function segmentAccuracyBreakdown(CampaignService $service)
{
    $plans = CampaignPlan::whereNotNull('approved_at')->get();

    $stats = [
        'Premium' => ['correct' => 0, 'total' => 0],
        'Standard' => ['correct' => 0, 'total' => 0],
        'Low' => ['correct' => 0, 'total' => 0]
    ];

    foreach ($plans as $plan) {
        $seller = Seller::find($plan->seller_id);
        $updateCount = $seller->products()->where('updated_at', '>', now()->subDays(7))->count();
        $exportUsage = $seller->exports()->count();

        $inputs = [
            'response_time' => $seller->response_time,
            'comment_response_rate' => $seller->comment_response_rate,
            'update_count' => $updateCount,
            'export_usage' => $exportUsage
        ];

        $suggestion = $service->suggestSegmentedCampaign($inputs);
        $expected = $suggestion['segment'];

        $actual = match ($plan->campaign_type) {
            'Outlet Boost' => 'Premium',
            'Flash Sale' => 'Standard',
            default => 'Low'
        };

        $stats[$actual]['total']++;
        if ($expected === $actual) {
            $stats[$actual]['correct']++;
        }
    }

    return collect($stats)->map(fn($s) => [
        'accuracy_percent' => $s['total'] > 0 ? round(($s['correct'] / $s['total']) * 100, 2) : 0,
        'correct' => $s['correct'],
        'total' => $s['total']
    ]);
    }

    /**
     * Segment listesi (Admin için)
     */
    public function segments()
    {
        $segments = Cache::remember('admin_segments', 3600, function () {
            return [
                ['id' => 1, 'name' => 'Premium', 'color' => '#10B981', 'min_score' => 80, 'description' => 'Yüksek performanslı satıcılar'],
                ['id' => 2, 'name' => 'Standard', 'color' => '#F59E0B', 'min_score' => 50, 'description' => 'Orta performanslı satıcılar'],
                ['id' => 3, 'name' => 'Low', 'color' => '#EF4444', 'min_score' => 0, 'description' => 'Geliştirilmesi gereken satıcılar'],
            ];
        });

        return response()->json($segments);
    }

    /**
     * Segment ayarlarını güncelle (Admin için)
     */
    public function updateSegments(Request $request)
    {
        $request->validate([
            '*.id' => 'required|integer',
            '*.name' => 'required|string|max:50',
            '*.min_score' => 'required|integer|min:0|max:100',
        ]);

        Cache::put('admin_segments', $request->all(), 86400);

        return response()->json([
            'success' => true,
            'message' => 'Segment ayarları güncellendi'
        ]);
    }

    /**
     * Segment başarı tahminleri (Admin için)
     */
    public function segmentSuccessPredictions(CampaignService $service)
    {
        $predictions = [];
        
        $sellers = User::where('role', 'seller')
            ->with(['products' => function($q) {
                $q->withCount('reviews');
            }])
            ->take(50)
            ->get();

        foreach ($sellers as $seller) {
            $updateCount = $seller->products()->where('updated_at', '>', now()->subDays(7))->count();
            $productCount = $seller->products()->count();
            
            $inputs = [
                'response_time' => $seller->response_time ?? rand(1, 48),
                'comment_response_rate' => $seller->comment_response_rate ?? rand(50, 100),
                'update_count' => $updateCount,
                'product_count' => $productCount
            ];

            $suggestion = $service->suggestSegmentedCampaign($inputs);

            $predictions[] = [
                'seller_id' => $seller->id,
                'seller_name' => $seller->name,
                'predicted_segment' => $suggestion['segment'],
                'confidence' => $suggestion['confidence'] ?? rand(70, 95),
                'suggested_campaign' => $suggestion['campaign_type'] ?? 'Standard',
                'metrics' => $inputs
            ];
        }

        return response()->json([
            'predictions' => $predictions,
            'total' => count($predictions),
            'generated_at' => now()->toISOString()
        ]);
    }

    private function ensureSellerContext(Request $request): User
    {
        // GEÇĠCĠ: Auth olmadan ilk seller'ı döndür (geliştirme için)
        $user = $request->user();
        
        if (!$user) {
            // Get first seller for development
            $seller = User::where('role', 'seller')->first();
            if (!$seller) {
                abort(404, 'Sistem satıcı hesabı bulunamadı.');
            }
            return $seller;
        }
        
        abort_if(!$user->isSeller(), 403, 'Satıcı hesabı gerekiyor.');

        return $user;
    }
}