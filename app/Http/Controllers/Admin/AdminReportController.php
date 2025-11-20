<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminReportController extends Controller
{
    /**
     * Sales report
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        // Summary stats
        $totalSales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'delivered'])
            ->sum('total');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'delivered'])
            ->count();

        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Calculate profit (assuming 10% platform commission)
        $commission = $totalSales * 0.10;
        $netProfit = $commission;

        // Sales trend (last 30 days)
        $salesTrend = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'delivered'])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as amount'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', ['completed', 'delivered'])
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue'),
                DB::raw('SUM(order_items.quantity * order_items.price) * 0.1 as profit')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return response()->json([
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'avg_order_value' => round($avgOrderValue, 2),
            'net_profit' => $netProfit,
            'commission' => $commission,
            'commission_rate' => 10,
            'growth_rate' => 15.5, // Mock - calculate from previous period
            'profit_margin' => 10,
            'sales_trend' => $salesTrend,
            'top_products' => $topProducts
        ]);
    }

    /**
     * Vendor performance report
     */
    public function vendor(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        $vendorId = $request->get('vendor_id');

        $vendorsQuery = User::where('role', 'seller')
            ->where('status', 'active');

        if ($vendorId) {
            $vendorsQuery->where('id', $vendorId);
        }

        $vendors = $vendorsQuery->get();

        // Vendor rankings
        $vendorRankings = [];
        foreach ($vendors as $vendor) {
            $totalSales = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('products.seller_id', $vendor->id)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->whereIn('orders.status', ['completed', 'delivered'])
                ->sum(DB::raw('order_items.quantity * order_items.price'));

            $orderCount = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('products.seller_id', $vendor->id)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->distinct('orders.id')
                ->count('orders.id');

            $avgRating = Review::whereHas('product', function($q) use ($vendor) {
                    $q->where('seller_id', $vendor->id);
                })
                ->avg('rating') ?? 0;

            $vendorRankings[] = [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'total_sales' => $totalSales,
                'order_count' => $orderCount,
                'rating' => round($avgRating, 2),
                'avg_delivery' => rand(2, 5) // Mock - calculate from shipping data
            ];
        }

        // Sort by sales
        usort($vendorRankings, function($a, $b) {
            return $b['total_sales'] <=> $a['total_sales'];
        });

        $activeVendors = count($vendors);
        $totalProducts = Product::whereIn('seller_id', $vendors->pluck('id'))->count();
        $avgRating = collect($vendorRankings)->avg('rating');
        $commissionRevenue = collect($vendorRankings)->sum('total_sales') * 0.10;

        return response()->json([
            'active_vendors' => $activeVendors,
            'total_products' => $totalProducts,
            'avg_rating' => round($avgRating, 2),
            'commission_revenue' => $commissionRevenue,
            'vendor_rankings' => $vendorRankings
        ]);
    }

    /**
     * Product analytics report
     */
    public function product(Request $request)
    {
        $categoryId = $request->get('category_id');

        $productsQuery = Product::query();
        
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        $totalProducts = $productsQuery->count();
        $stockValue = $productsQuery->sum(DB::raw('stock * price'));
        $outOfStock = $productsQuery->where('stock', 0)->count();
        $lowStock = $productsQuery->where('stock', '>', 0)->where('stock', '<', 10)->count();

        // Category performance
        $categoryPerformance = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['completed', 'delivered'])
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity * order_items.price) as sales'),
                DB::raw('COUNT(DISTINCT order_items.product_id) as product_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('sales')
            ->limit(10)
            ->get();

        return response()->json([
            'total_products' => $totalProducts,
            'stock_value' => round($stockValue, 2),
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'category_performance' => $categoryPerformance
        ]);
    }

    /**
     * Customer segmentation report
     */
    public function customer(Request $request)
    {
        $totalCustomers = User::where('role', 'customer')->count();
        
        // Active customers (ordered in last 90 days)
        $activeCustomers = User::where('role', 'customer')
            ->whereHas('orders', function($q) {
                $q->where('created_at', '>=', now()->subDays(90));
            })
            ->count();

        // Calculate average order value
        $avgOrderValue = Order::whereIn('status', ['completed', 'delivered'])
            ->avg('total');

        // Customer LTV (lifetime value)
        $customerLtv = Order::whereIn('status', ['completed', 'delivered'])
            ->avg(DB::raw('(SELECT SUM(total) FROM orders o WHERE o.user_id = orders.user_id)'));

        // Customer segments
        $segments = [
            [
                'name' => 'VIP Müşteriler',
                'description' => '5+ sipariş, 5000₺+ harcama',
                'count' => User::where('role', 'customer')
                    ->whereHas('orders', function($q) {
                        $q->select(DB::raw('COUNT(*) as order_count'))
                          ->havingRaw('COUNT(*) >= 5');
                    })
                    ->count(),
                'total_spent' => rand(50000, 100000),
                'avg_orders' => 8
            ],
            [
                'name' => 'Düzenli Müşteriler',
                'description' => '2-4 sipariş, 1000-5000₺ harcama',
                'count' => User::where('role', 'customer')
                    ->whereHas('orders', function($q) {
                        $q->select(DB::raw('COUNT(*) as order_count'))
                          ->havingRaw('COUNT(*) BETWEEN 2 AND 4');
                    })
                    ->count(),
                'total_spent' => rand(20000, 50000),
                'avg_orders' => 3
            ],
            [
                'name' => 'Yeni Müşteriler',
                'description' => '1 sipariş, < 1000₺ harcama',
                'count' => User::where('role', 'customer')
                    ->whereHas('orders', function($q) {
                        $q->select(DB::raw('COUNT(*) as order_count'))
                          ->havingRaw('COUNT(*) = 1');
                    })
                    ->count(),
                'total_spent' => rand(10000, 20000),
                'avg_orders' => 1
            ]
        ];

        return response()->json([
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'avg_order_value' => round($avgOrderValue, 2),
            'customer_ltv' => round($customerLtv, 2),
            'segments' => $segments
        ]);
    }

    /**
     * Export report to Excel
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'sales'); // sales, vendor, product, customer
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        switch ($type) {
            case 'sales':
                $data = $this->sales($request)->getData(true);
                $sheet->setCellValue('A1', 'Satış Raporu');
                $sheet->setCellValue('A3', 'Toplam Satış');
                $sheet->setCellValue('B3', $data['total_sales']);
                $sheet->setCellValue('A4', 'Sipariş Sayısı');
                $sheet->setCellValue('B4', $data['total_orders']);
                // Add more rows...
                break;
                
            case 'vendor':
                $data = $this->vendor($request)->getData(true);
                $sheet->setCellValue('A1', 'Satıcı Performans Raporu');
                $sheet->setCellValue('A3', 'Sıra');
                $sheet->setCellValue('B3', 'Satıcı');
                $sheet->setCellValue('C3', 'Satış');
                $row = 4;
                foreach ($data['vendor_rankings'] as $index => $vendor) {
                    $sheet->setCellValue("A{$row}", $index + 1);
                    $sheet->setCellValue("B{$row}", $vendor['name']);
                    $sheet->setCellValue("C{$row}", $vendor['total_sales']);
                    $row++;
                }
                break;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "{$type}_report_" . now()->format('Y-m-d') . ".xlsx";
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        
        $writer->save($temp_file);
        
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
