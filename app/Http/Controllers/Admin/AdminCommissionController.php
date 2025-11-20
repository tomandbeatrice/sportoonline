<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyCommission;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class AdminCommissionController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Get all sellers' commission summary
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $status = $request->get('status');

        $query = MonthlyCommission::with(['user', 'subscription.plan'])
            ->where('month', $month);

        if ($status) {
            $query->where('status', $status);
        }

        $commissions = $query->orderBy('net_commission', 'desc')
            ->paginate(20);

        // Calculate totals
        $totals = MonthlyCommission::where('month', $month)
            ->selectRaw('
                SUM(total_sales) as total_sales,
                SUM(commission_amount) as total_commission,
                SUM(subscription_fee) as total_subscription_fees,
                SUM(net_commission) as total_net_commission,
                COUNT(*) as seller_count
            ')
            ->first();

        return response()->json([
            'month' => $month,
            'commissions' => $commissions,
            'summary' => [
                'total_sales' => $totals->total_sales ?? 0,
                'total_commission' => $totals->total_commission ?? 0,
                'total_subscription_fees' => $totals->total_subscription_fees ?? 0,
                'total_net_commission' => $totals->total_net_commission ?? 0,
                'seller_count' => $totals->seller_count ?? 0,
            ],
        ]);
    }

    /**
     * Get specific seller's commission details
     */
    public function show(int $userId, string $month)
    {
        $user = User::findOrFail($userId);

        $summary = $this->commissionService->getSellerCommissionSummary($user, $month);

        // Get order details for this month
        $commission = MonthlyCommission::where('user_id', $userId)
            ->where('month', $month)
            ->with('subscription.plan')
            ->first();

        return response()->json([
            'seller' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'month' => $month,
            'summary' => $summary,
            'commission_record' => $commission,
        ]);
    }

    /**
     * Mark commission as paid
     */
    public function markAsPaid(int $commissionId)
    {
        $commission = MonthlyCommission::findOrFail($commissionId);

        if ($commission->status === 'paid') {
            return response()->json([
                'error' => 'Bu komisyon zaten ödenmiş olarak işaretlenmiş',
            ], 400);
        }

        $commission->markAsPaid();

        return response()->json([
            'message' => 'Komisyon ödendi olarak işaretlendi',
            'commission' => $commission,
        ]);
    }

    /**
     * Calculate commission for specific month
     */
    public function calculateMonth(Request $request)
    {
        $month = $request->get('month', now()->subMonth()->format('Y-m'));

        // Get all sellers with active subscriptions
        $sellers = User::whereHas('subscriptions', function($query) use ($month) {
                $query->where('status', 'active')
                    ->where('starts_at', '<=', $month . '-01')
                    ->where('ends_at', '>=', $month . '-01');
            })
            ->get();

        $results = [];
        $errors = [];

        foreach ($sellers as $seller) {
            try {
                $commission = $this->commissionService->calculateMonthlyCommission($seller, $month);
                $results[] = [
                    'seller_id' => $seller->id,
                    'seller_name' => $seller->name,
                    'commission_id' => $commission->id,
                    'net_commission' => $commission->net_commission,
                ];
            } catch (\Exception $e) {
                $errors[] = [
                    'seller_id' => $seller->id,
                    'seller_name' => $seller->name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'month' => $month,
            'calculated' => count($results),
            'errors' => count($errors),
            'results' => $results,
            'error_details' => $errors,
        ]);
    }

    /**
     * Get commission statistics
     */
    public function statistics(Request $request)
    {
        $year = $request->get('year', now()->year);

        $monthlyStats = MonthlyCommission::whereYear('month', $year)
            ->selectRaw('
                month,
                SUM(total_sales) as total_sales,
                SUM(commission_amount) as total_commission,
                SUM(subscription_fee) as total_subscription_fees,
                SUM(net_commission) as total_net_commission,
                COUNT(DISTINCT user_id) as seller_count,
                AVG(net_commission) as avg_net_commission
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get top sellers by commission
        $topSellers = MonthlyCommission::whereYear('month', $year)
            ->selectRaw('
                user_id,
                SUM(total_sales) as total_sales,
                SUM(commission_amount) as total_commission,
                SUM(net_commission) as total_net_commission
            ')
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderBy('total_net_commission', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'year' => $year,
            'monthly_breakdown' => $monthlyStats,
            'top_sellers' => $topSellers,
        ]);
    }
}
