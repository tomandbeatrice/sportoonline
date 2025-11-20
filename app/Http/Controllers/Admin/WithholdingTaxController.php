<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderCalculationService;
use Illuminate\Http\Request;

class WithholdingTaxController extends Controller
{
    protected $calculationService;

    public function __construct(OrderCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    /**
     * Get withholding tax summary for specific month
     */
    public function monthlySummary(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));

        [$year, $monthNum] = explode('-', $month);

        $orders = Order::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->with(['order_items.product.user'])
            ->get();

        $totalWithholdingTax = $orders->sum('withholding_tax_amount');
        $totalShippingFees = $orders->sum('shipping_fee');
        $totalCommission = $orders->sum(function($order) {
            return $order->order_items->sum('subscription_commission_amount');
        });

        // Group by tax rate
        $byTaxRate = $orders->groupBy('withholding_tax_rate')->map(function($group, $rate) {
            return [
                'rate' => $rate,
                'count' => $group->count(),
                'total_tax' => $group->sum('withholding_tax_amount'),
                'total_sales' => $group->sum('subtotal'),
            ];
        })->values();

        // Group by seller
        $bySeller = [];
        foreach ($orders as $order) {
            $sellerId = $order->order_items->first()->product->user_id;
            $sellerName = $order->order_items->first()->product->user->name;

            if (!isset($bySeller[$sellerId])) {
                $bySeller[$sellerId] = [
                    'seller_id' => $sellerId,
                    'seller_name' => $sellerName,
                    'total_sales' => 0,
                    'total_tax' => 0,
                    'total_shipping' => 0,
                    'total_commission' => 0,
                    'order_count' => 0,
                ];
            }

            $bySeller[$sellerId]['total_sales'] += $order->subtotal;
            $bySeller[$sellerId]['total_tax'] += $order->withholding_tax_amount;
            $bySeller[$sellerId]['total_shipping'] += $order->shipping_fee;
            $bySeller[$sellerId]['total_commission'] += $order->order_items->sum('subscription_commission_amount');
            $bySeller[$sellerId]['order_count']++;
        }

        return response()->json([
            'month' => $month,
            'summary' => [
                'total_withholding_tax' => $totalWithholdingTax,
                'total_shipping_fees' => $totalShippingFees,
                'total_commission' => $totalCommission,
                'total_platform_revenue' => $totalWithholdingTax + $totalShippingFees + $totalCommission,
                'order_count' => $orders->count(),
            ],
            'by_tax_rate' => $byTaxRate,
            'by_seller' => array_values($bySeller),
        ]);
    }

    /**
     * Get annual withholding tax report
     */
    public function annualReport(Request $request)
    {
        $year = $request->get('year', now()->year);

        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStr = sprintf('%d-%02d', $year, $month);
            
            $orders = Order::where('status', 'completed')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->get();

            $monthlyData[] = [
                'month' => $monthStr,
                'total_tax' => $orders->sum('withholding_tax_amount'),
                'total_shipping' => $orders->sum('shipping_fee'),
                'total_commission' => $orders->sum(function($order) {
                    return $order->order_items->sum('subscription_commission_amount');
                }),
                'order_count' => $orders->count(),
            ];
        }

        $yearlyTotal = Order::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->get();

        return response()->json([
            'year' => $year,
            'monthly_breakdown' => $monthlyData,
            'yearly_summary' => [
                'total_withholding_tax' => $yearlyTotal->sum('withholding_tax_amount'),
                'total_shipping_fees' => $yearlyTotal->sum('shipping_fee'),
                'total_commission' => $yearlyTotal->sum(function($order) {
                    return $order->order_items->sum('subscription_commission_amount');
                }),
                'total_orders' => $yearlyTotal->count(),
            ],
        ]);
    }

    /**
     * Export withholding tax data for accounting
     */
    public function export(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $monthNum] = explode('-', $month);

        $orders = Order::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->with(['order_items.product.user', 'user'])
            ->get();

        $exportData = $orders->map(function($order) {
            $seller = $order->order_items->first()->product->user;
            
            return [
                'order_id' => $order->id,
                'order_date' => $order->created_at->format('Y-m-d'),
                'seller_name' => $seller->name,
                'seller_tax_number' => $seller->tax_number ?? 'Bireysel',
                'customer_name' => $order->user->name,
                'subtotal' => $order->subtotal,
                'shipping_fee' => $order->shipping_fee,
                'commission_rate' => $order->order_items->first()->subscription_commission_rate ?? 20,
                'commission_amount' => $order->order_items->sum('subscription_commission_amount'),
                'withholding_tax_rate' => $order->withholding_tax_rate,
                'withholding_tax_amount' => $order->withholding_tax_amount,
                'seller_net_amount' => $order->seller_net_amount,
                'platform_revenue' => $order->platform_revenue,
            ];
        });

        return response()->json([
            'month' => $month,
            'data' => $exportData,
            'total_records' => $exportData->count(),
        ]);
    }
}
