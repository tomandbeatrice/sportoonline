<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingFee;
use App\Models\User;

class OrderCalculationService
{
    /**
     * Calculate E-Commerce withholding tax rate based on seller type
     * Yeni yasa: Gerçek kişi %2, Kurumsal %1
     */
    public function getWithholdingTaxRate(User $seller): float
    {
        // Eğer satıcı kurumsal (şirket) ise %1, bireysel ise %2
        if ($seller->business_type === 'corporate' || $seller->tax_number) {
            return 1.00; // %1 kurumsal stopaj
        }

        return 2.00; // %2 bireysel stopaj
    }

    /**
     * Calculate shipping fee for order
     */
    public function calculateShippingFee(User $seller, string $city, float $orderSubtotal): array
    {
        $region = ShippingFee::getCityRegion($city);
        
        $shippingFee = ShippingFee::where('user_id', $seller->id)
            ->where('region', $region)
            ->where('is_active', true)
            ->first();

        if (!$shippingFee) {
            // Default shipping fee if seller hasn't set custom rates
            return [
                'fee' => 30.00, // Default ₺30
                'region' => $region,
                'free_shipping' => false,
            ];
        }

        $fee = $shippingFee->calculateFee($orderSubtotal);
        $freeShipping = $shippingFee->qualifiesForFreeShipping($orderSubtotal);

        return [
            'fee' => $fee,
            'region' => $region,
            'free_shipping' => $freeShipping,
            'threshold' => $shippingFee->free_shipping_threshold,
        ];
    }

    /**
     * Calculate complete order breakdown with commission, shipping, and tax
     */
    public function calculateOrderFinancials(Order $order): array
    {
        $subtotal = $order->order_items->sum('subtotal');
        $seller = $order->order_items->first()->product->user;
        
        // 1. Komisyon hesaplama
        $subscription = $seller->subscriptions()->where('status', 'active')->first();
        $commissionRate = $subscription ? $subscription->commission_rate : 20.00;
        $commissionAmount = $subtotal * ($commissionRate / 100);

        // 2. Kargo ücreti hesaplama
        $shippingData = $this->calculateShippingFee(
            $seller,
            $order->shipping_city ?? 'İstanbul',
            $subtotal
        );
        $shippingFee = $shippingData['fee'];

        // 3. E-Ticaret Stopaj hesaplama (Satış tutarı üzerinden)
        $withholdingTaxRate = $this->getWithholdingTaxRate($seller);
        $withholdingTaxAmount = $subtotal * ($withholdingTaxRate / 100);

        // 4. Satıcının net kazancı
        $sellerNetAmount = $subtotal - $commissionAmount - $shippingFee - $withholdingTaxAmount;

        // 5. Platform toplam geliri
        $platformRevenue = $commissionAmount + $shippingFee + $withholdingTaxAmount;

        // 6. Müşteri toplam ödemesi
        $customerTotal = $subtotal + $shippingFee;

        return [
            'subtotal' => $subtotal,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'shipping_fee' => $shippingFee,
            'shipping_region' => $shippingData['region'],
            'free_shipping_applied' => $shippingData['free_shipping'],
            'withholding_tax_rate' => $withholdingTaxRate,
            'withholding_tax_amount' => $withholdingTaxAmount,
            'seller_net_amount' => $sellerNetAmount,
            'platform_revenue' => $platformRevenue,
            'customer_total' => $customerTotal,
            'breakdown' => [
                'customer_pays' => $customerTotal,
                'seller_receives' => $sellerNetAmount,
                'platform_receives' => $platformRevenue,
                'deductions' => [
                    'commission' => $commissionAmount,
                    'shipping' => $shippingFee,
                    'withholding_tax' => $withholdingTaxAmount,
                ],
            ],
        ];
    }

    /**
     * Update order with calculated amounts
     */
    public function updateOrderFinancials(Order $order): void
    {
        $financials = $this->calculateOrderFinancials($order);

        $order->update([
            'shipping_fee' => $financials['shipping_fee'],
            'shipping_region' => $financials['shipping_region'],
            'free_shipping_applied' => $financials['free_shipping_applied'],
            'withholding_tax_rate' => $financials['withholding_tax_rate'],
            'withholding_tax_amount' => $financials['withholding_tax_amount'],
            'seller_net_amount' => $financials['seller_net_amount'],
            'platform_revenue' => $financials['platform_revenue'],
            'total_amount' => $financials['customer_total'],
        ]);

        // Update each order item with withholding tax
        foreach ($order->order_items as $item) {
            $itemTax = $item->subtotal * ($financials['withholding_tax_rate'] / 100);
            $item->update([
                'withholding_tax_amount' => $itemTax,
            ]);
        }
    }

    /**
     * Calculate monthly totals including shipping and tax
     */
    public function calculateMonthlyTotals(User $seller, string $month): array
    {
        $orders = Order::whereHas('order_items.product', function($query) use ($seller) {
                $query->where('user_id', $seller->id);
            })
            ->where('status', 'completed')
            ->whereYear('created_at', '=', substr($month, 0, 4))
            ->whereMonth('created_at', '=', substr($month, 5, 2))
            ->get();

        $totalSales = $orders->sum('subtotal');
        $totalCommission = $orders->sum(function($order) use ($seller) {
            return $order->order_items()
                ->whereHas('product', function($q) use ($seller) {
                    $q->where('user_id', $seller->id);
                })
                ->sum('subscription_commission_amount');
        });
        
        $totalShipping = $orders->sum('shipping_fee');
        $totalWithholdingTax = $orders->sum('withholding_tax_amount');
        
        // Subscription fee
        $subscription = $seller->subscriptions()
            ->where('status', 'active')
            ->where('starts_at', '<=', $month . '-01')
            ->where('ends_at', '>=', $month . '-01')
            ->first();
        
        $subscriptionFee = $subscription ? $subscription->amount : 0;

        // Net commission calculation
        $netCommission = $totalCommission - $subscriptionFee - $totalShipping - $totalWithholdingTax;

        return [
            'total_sales' => $totalSales,
            'total_commission' => $totalCommission,
            'subscription_fee' => $subscriptionFee,
            'total_shipping_fees' => $totalShipping,
            'total_withholding_tax' => $totalWithholdingTax,
            'net_commission' => $netCommission,
            'order_count' => $orders->count(),
        ];
    }
}
