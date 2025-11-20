<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Subscription;
use App\Models\MonthlyCommission;
use Carbon\Carbon;

class CommissionService
{
    /**
     * Calculate commission for an order item based on seller's subscription
     */
    public function calculateOrderItemCommission(OrderItem $orderItem): array
    {
        $seller = $orderItem->product->user;
        $subscription = $this->getActiveSubscription($seller);

        if (!$subscription) {
            // Default commission if no subscription
            return [
                'rate' => 15.00,
                'amount' => $orderItem->subtotal * 0.15,
                'subscription_plan' => null,
            ];
        }

        $commissionRate = $subscription->commission_rate;
        $commissionAmount = $orderItem->subtotal * ($commissionRate / 100);

        return [
            'rate' => $commissionRate,
            'amount' => $commissionAmount,
            'subscription_plan' => $subscription->plan->name,
        ];
    }

    /**
     * Get active subscription for a user
     */
    public function getActiveSubscription(User $user): ?Subscription
    {
        return Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Calculate monthly commission for a seller
     */
    public function calculateMonthlyCommission(User $seller, string $month): MonthlyCommission
    {
        $subscription = $this->getActiveSubscription($seller);
        
        if (!$subscription) {
            throw new \Exception('Satıcının aktif aboneliği yok');
        }

        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get all orders for this seller in this month
        $orderItems = OrderItem::whereHas('product', function($query) use ($seller) {
                $query->where('user_id', $seller->id);
            })
            ->whereHas('order', function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with('order')
            ->get();

        $totalSales = $orderItems->sum('subtotal');
        $commissionRate = $subscription->commission_rate;
        $commissionAmount = $totalSales * ($commissionRate / 100);
        $subscriptionFee = $subscription->amount;
        $netCommission = $commissionAmount - $subscriptionFee;

        // Create or update monthly commission record
        $monthlyCommission = MonthlyCommission::updateOrCreate(
            [
                'user_id' => $seller->id,
                'month' => $month,
            ],
            [
                'subscription_id' => $subscription->id,
                'total_sales' => $totalSales,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'subscription_fee' => $subscriptionFee,
                'net_commission' => $netCommission,
                'order_count' => $orderItems->count(),
                'status' => 'calculated',
            ]
        );

        return $monthlyCommission;
    }

    /**
     * Get seller's commission summary
     */
    public function getSellerCommissionSummary(User $seller, ?string $month = null): array
    {
        $month = $month ?? now()->format('Y-m');
        $subscription = $this->getActiveSubscription($seller);

        if (!$subscription) {
            return [
                'has_subscription' => false,
                'message' => 'Aktif abonelik bulunamadı',
            ];
        }

        $monthlyCommission = MonthlyCommission::where('user_id', $seller->id)
            ->where('month', $month)
            ->first();

        if (!$monthlyCommission) {
            $monthlyCommission = $this->calculateMonthlyCommission($seller, $month);
        }

        return [
            'has_subscription' => true,
            'plan_name' => $subscription->plan->name,
            'subscription_fee' => $subscription->amount,
            'commission_rate' => $subscription->commission_rate,
            'month' => $month,
            'total_sales' => $monthlyCommission->total_sales,
            'gross_commission' => $monthlyCommission->commission_amount,
            'subscription_fee_deducted' => $monthlyCommission->subscription_fee,
            'net_commission' => $monthlyCommission->net_commission,
            'order_count' => $monthlyCommission->order_count,
            'status' => $monthlyCommission->status,
            'breakdown' => [
                'monthly_fee' => $subscription->amount,
                'commission_percentage' => $subscription->commission_rate,
                'gross_commission' => $monthlyCommission->commission_amount,
                'net_amount' => $monthlyCommission->net_commission,
            ],
        ];
    }

    /**
     * Get yearly commission summary
     */
    public function getYearlyCommissionSummary(User $seller, int $year = null): array
    {
        $year = $year ?? now()->year;
        
        $monthlyCommissions = MonthlyCommission::where('user_id', $seller->id)
            ->where('month', 'like', $year . '-%')
            ->get();

        $totalSales = $monthlyCommissions->sum('total_sales');
        $totalGrossCommission = $monthlyCommissions->sum('commission_amount');
        $totalSubscriptionFees = $monthlyCommissions->sum('subscription_fee');
        $totalNetCommission = $monthlyCommissions->sum('net_commission');

        return [
            'year' => $year,
            'total_sales' => $totalSales,
            'total_gross_commission' => $totalGrossCommission,
            'total_subscription_fees' => $totalSubscriptionFees,
            'total_net_commission' => $totalNetCommission,
            'months_active' => $monthlyCommissions->count(),
            'monthly_breakdown' => $monthlyCommissions->map(function($mc) {
                return [
                    'month' => $mc->month,
                    'sales' => $mc->total_sales,
                    'commission' => $mc->commission_amount,
                    'net' => $mc->net_commission,
                ];
            }),
        ];
    }

    /**
     * Calculate commission comparison between plans
     */
    public function compareCommissionPlans(float $estimatedMonthlySales): array
    {
        $plans = [
            ['name' => 'Sadece Komisyon', 'monthly_fee' => 0, 'commission_rate' => 20, 'product_limit' => 999999],
            ['name' => 'Küçük Paket', 'monthly_fee' => 49, 'commission_rate' => 12, 'product_limit' => 50],
            ['name' => 'Orta Paket', 'monthly_fee' => 149, 'commission_rate' => 10, 'product_limit' => 200],
            ['name' => 'Büyük Paket', 'monthly_fee' => 399, 'commission_rate' => 8, 'product_limit' => 1000],
            ['name' => 'Kurumsal Paket', 'monthly_fee' => 999, 'commission_rate' => 5, 'product_limit' => 999999],
        ];

        $comparison = [];

        foreach ($plans as $plan) {
            $grossCommission = $estimatedMonthlySales * ($plan['commission_rate'] / 100);
            $netCommission = $grossCommission - $plan['monthly_fee'];
            $totalCost = $plan['monthly_fee'] + $grossCommission;
            $effectiveRate = $estimatedMonthlySales > 0 ? ($totalCost / $estimatedMonthlySales) * 100 : 0;

            $comparison[] = [
                'plan' => $plan['name'],
                'monthly_fee' => $plan['monthly_fee'],
                'commission_rate' => $plan['commission_rate'],
                'product_limit' => $plan['product_limit'],
                'estimated_sales' => $estimatedMonthlySales,
                'gross_commission' => $grossCommission,
                'net_commission' => $netCommission,
                'total_cost' => $totalCost,
                'effective_commission_rate' => round($effectiveRate, 2),
                'you_keep' => $estimatedMonthlySales - $totalCost, // Satıcının elinde kalan
            ];
        }

        // Sort by best net commission (highest to lowest)
        usort($comparison, function($a, $b) {
            return $b['you_keep'] <=> $a['you_keep'];
        });

        return $comparison;
    }

    /**
     * Get recommended plan based on sales volume
     */
    public function getRecommendedPlan(float $estimatedMonthlySales): array
    {
        $comparison = $this->compareCommissionPlans($estimatedMonthlySales);
        
        // Best plan is the one seller keeps the most money
        $bestPlan = $comparison[0];

        // Calculate break-even points for subscription plans
        $breakEven = [
            'Küçük Paket' => 49 / ((20 - 12) / 100), // ₺612.50
            'Orta Paket' => 149 / ((20 - 10) / 100), // ₺1,490
            'Büyük Paket' => 399 / ((20 - 8) / 100), // ₺3,325
            'Kurumsal Paket' => 999 / ((20 - 5) / 100), // ₺6,660
        ];

        $recommendation = '';
        if ($estimatedMonthlySales < 613) {
            $recommendation = 'Düşük satış hacmi için Sadece Komisyon modeli daha avantajlı';
        } elseif ($estimatedMonthlySales < 1500) {
            $recommendation = 'Bu satış hacmi için Küçük Paket en karlı seçenek';
        } elseif ($estimatedMonthlySales < 3400) {
            $recommendation = 'Bu satış hacmi için Orta Paket en karlı seçenek';
        } elseif ($estimatedMonthlySales < 6700) {
            $recommendation = 'Bu satış hacmi için Büyük Paket en karlı seçenek';
        } else {
            $recommendation = 'Yüksek satış hacmi için Kurumsal Paket en karlı seçenek';
        }

        return [
            'recommended_plan' => $bestPlan['plan'],
            'reason' => $recommendation,
            'you_keep' => $bestPlan['you_keep'],
            'break_even_points' => $breakEven,
            'comparison' => $comparison,
        ];
    }
}
