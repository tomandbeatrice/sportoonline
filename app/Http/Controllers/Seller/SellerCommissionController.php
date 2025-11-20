<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class SellerCommissionController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Get seller's commission history
     */
    public function index(Request $request)
    {
        $seller = auth()->user();
        $year = $request->get('year', now()->year);

        $summary = $this->commissionService->getYearlyCommissionSummary($seller, $year);

        return response()->json($summary);
    }

    /**
     * Get current month commission summary
     */
    public function currentMonth()
    {
        $seller = auth()->user();
        $month = now()->format('Y-m');

        $summary = $this->commissionService->getSellerCommissionSummary($seller, $month);

        return response()->json($summary);
    }

    /**
     * Get specific month commission details
     */
    public function show(string $month)
    {
        $seller = auth()->user();

        // Validate month format (YYYY-MM)
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return response()->json([
                'error' => 'Geçersiz ay formatı. YYYY-MM formatında olmalıdır.',
            ], 400);
        }

        $summary = $this->commissionService->getSellerCommissionSummary($seller, $month);

        return response()->json($summary);
    }

    /**
     * Get commission forecast for current month
     */
    public function forecast()
    {
        $seller = auth()->user();
        $month = now()->format('Y-m');

        // Calculate current month's running total
        $summary = $this->commissionService->getSellerCommissionSummary($seller, $month);

        // Calculate daily average and project to month end
        $daysElapsed = now()->day;
        $daysInMonth = now()->daysInMonth;
        $dailyAverage = $summary['total_sales'] / $daysElapsed;
        $projectedSales = $dailyAverage * $daysInMonth;
        $projectedCommission = $projectedSales * ($summary['commission_rate'] / 100);
        $projectedNet = $projectedCommission - $summary['subscription_fee'];

        return response()->json([
            'current_month' => $month,
            'days_elapsed' => $daysElapsed,
            'days_remaining' => $daysInMonth - $daysElapsed,
            'current_sales' => $summary['total_sales'],
            'projected_sales' => round($projectedSales, 2),
            'current_commission' => $summary['gross_commission'],
            'projected_commission' => round($projectedCommission, 2),
            'subscription_fee' => $summary['subscription_fee'],
            'current_net' => $summary['net_commission'],
            'projected_net' => round($projectedNet, 2),
            'daily_average' => round($dailyAverage, 2),
        ]);
    }

    /**
     * Compare commission plans based on seller's sales
     */
    public function comparePlans(Request $request)
    {
        $estimatedSales = $request->get('estimated_sales', 10000);

        $comparison = $this->commissionService->compareCommissionPlans($estimatedSales);
        $recommended = $this->commissionService->getRecommendedPlan($estimatedSales);

        return response()->json([
            'estimated_sales' => $estimatedSales,
            'recommended' => $recommended,
            'all_plans' => $comparison,
        ]);
    }
}
