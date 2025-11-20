<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    public function metrics(Request $request)
    {
        $period = $this->getPeriod($request);
        $previousPeriod = $this->getPreviousPeriod($period);

        // Current period metrics
        $currentMetrics = $this->calculateMetrics($period['start'], $period['end']);
        
        // Previous period metrics for comparison
        $previousMetrics = $this->calculateMetrics($previousPeriod['start'], $previousPeriod['end']);

        // Calculate percentage changes
        $metrics = [
            'totalRevenue' => $currentMetrics['revenue'],
            'totalOrders' => $currentMetrics['orders'],
            'avgOrderValue' => $currentMetrics['orders'] > 0 
                ? $currentMetrics['revenue'] / $currentMetrics['orders'] 
                : 0,
            'conversionRate' => $currentMetrics['visitors'] > 0 
                ? ($currentMetrics['orders'] / $currentMetrics['visitors']) * 100 
                : 0,
            'revenueChange' => $this->calculateChange($currentMetrics['revenue'], $previousMetrics['revenue']),
            'ordersChange' => $this->calculateChange($currentMetrics['orders'], $previousMetrics['orders']),
            'avgChange' => $this->calculateChange(
                $currentMetrics['orders'] > 0 ? $currentMetrics['revenue'] / $currentMetrics['orders'] : 0,
                $previousMetrics['orders'] > 0 ? $previousMetrics['revenue'] / $previousMetrics['orders'] : 0
            ),
            'conversionChange' => $this->calculateChange(
                $currentMetrics['visitors'] > 0 ? ($currentMetrics['orders'] / $currentMetrics['visitors']) * 100 : 0,
                $previousMetrics['visitors'] > 0 ? ($previousMetrics['orders'] / $previousMetrics['visitors']) * 100 : 0
            ),
        ];

        return response()->json($metrics);
    }

    public function topProducts(Request $request)
    {
        $period = $this->getPeriod($request);

        $topProducts = OrderItem::select(
                'products.id',
                'products.name',
                'categories.name as category',
                DB::raw('SUM(order_items.quantity) as sales'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$period['start'], $period['end']])
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderByDesc('sales')
            ->limit(10)
            ->get();

        return response()->json($topProducts);
    }

    public function topSellers(Request $request)
    {
        $period = $this->getPeriod($request);

        $topSellers = Order::select(
                'vendors.id',
                'vendors.store_name as name',
                'users.email',
                DB::raw('COUNT(orders.id) as orders'),
                DB::raw('SUM(orders.total_amount) as revenue')
            )
            ->join('vendors', 'orders.vendor_id', '=', 'vendors.id')
            ->join('users', 'vendors.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$period['start'], $period['end']])
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('vendors.id', 'vendors.store_name', 'users.email')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return response()->json($topSellers);
    }

    public function daily(Request $request)
    {
        $period = $this->getPeriod($request);

        $dailyData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('AVG(total_amount) as avgOrder'),
                DB::raw('COUNT(DISTINCT user_id) as customers')
            )
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->where('status', '!=', 'cancelled')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item, $index, $collection) {
                // Calculate trend compared to previous day
                $trend = 0;
                if ($index > 0) {
                    $previous = $collection[$index - 1];
                    $trend = $this->calculateChange($item->revenue, $previous->revenue);
                }
                $item->trend = $trend;
                return $item;
            });

        return response()->json($dailyData);
    }

    public function summary(Request $request)
    {
        $period = $this->getPeriod($request);

        // Payment methods
        $paymentMethods = Order::select(
                'payment_method as method',
                DB::raw('SUM(total_amount) as amount'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->where('status', '!=', 'cancelled')
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) use ($period) {
                $total = Order::whereBetween('created_at', [$period['start'], $period['end']])
                    ->where('status', '!=', 'cancelled')
                    ->sum('total_amount');
                $item->percent = $total > 0 ? round(($item->amount / $total) * 100, 1) : 0;
                return $item;
            });

        // Shipping status
        $shippingStatus = Order::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->groupBy('status')
            ->get()
            ->map(function ($item) use ($period) {
                $total = Order::whereBetween('created_at', [$period['start'], $period['end']])
                    ->count();
                $item->percent = $total > 0 ? round(($item->count / $total) * 100, 1) : 0;
                return $item;
            });

        // Top cities (assuming address field exists)
        $topCities = Order::select(
                'shipping_city as name',
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('shipping_city')
            ->groupBy('shipping_city')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(function ($item) use ($period) {
                $total = Order::whereBetween('created_at', [$period['start'], $period['end']])
                    ->where('status', '!=', 'cancelled')
                    ->sum('total_amount');
                $item->percent = $total > 0 ? round(($item->revenue / $total) * 100, 1) : 0;
                return $item;
            });

        return response()->json([
            'paymentMethods' => $paymentMethods,
            'shippingStatus' => $shippingStatus,
            'topCities' => $topCities
        ]);
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'excel');
        $period = $this->getPeriod($request);

        // Get data
        $data = [
            'metrics' => $this->calculateMetrics($period['start'], $period['end']),
            'daily' => $this->daily($request)->getData(),
            'topProducts' => $this->topProducts($request)->getData(),
            'topSellers' => $this->topSellers($request)->getData(),
        ];

        if ($format === 'excel') {
            return $this->exportExcel($data, $period);
        } else {
            return $this->exportPDF($data, $period);
        }
    }

    private function getPeriod(Request $request): array
    {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            return [
                'start' => Carbon::parse($request->start_date)->startOfDay(),
                'end' => Carbon::parse($request->end_date)->endOfDay()
            ];
        }

        $period = $request->input('period', '7days');

        switch ($period) {
            case 'today':
                return [
                    'start' => Carbon::today(),
                    'end' => Carbon::now()
                ];
            case 'yesterday':
                return [
                    'start' => Carbon::yesterday()->startOfDay(),
                    'end' => Carbon::yesterday()->endOfDay()
                ];
            case '7days':
                return [
                    'start' => Carbon::now()->subDays(7),
                    'end' => Carbon::now()
                ];
            case '30days':
                return [
                    'start' => Carbon::now()->subDays(30),
                    'end' => Carbon::now()
                ];
            case 'thisMonth':
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()
                ];
            case 'lastMonth':
                return [
                    'start' => Carbon::now()->subMonth()->startOfMonth(),
                    'end' => Carbon::now()->subMonth()->endOfMonth()
                ];
            case 'thisYear':
                return [
                    'start' => Carbon::now()->startOfYear(),
                    'end' => Carbon::now()
                ];
            default:
                return [
                    'start' => Carbon::now()->subDays(7),
                    'end' => Carbon::now()
                ];
        }
    }

    private function getPreviousPeriod(array $period): array
    {
        $diff = $period['start']->diffInDays($period['end']);
        
        return [
            'start' => $period['start']->copy()->subDays($diff + 1),
            'end' => $period['start']->copy()->subDay()
        ];
    }

    private function calculateMetrics($start, $end): array
    {
        $revenue = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $orders = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->count();

        // Simplified visitor count (could be replaced with actual analytics)
        $visitors = $orders * 5; // Assuming 20% conversion rate

        return [
            'revenue' => $revenue,
            'orders' => $orders,
            'visitors' => $visitors
        ];
    }

    private function calculateChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function exportExcel($data, $period)
    {
        // Simple CSV export
        $filename = 'rapor-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Metrics
            fputcsv($file, ['GENEL METRİKLER']);
            fputcsv($file, ['Toplam Gelir', $data['metrics']['revenue']]);
            fputcsv($file, ['Toplam Sipariş', $data['metrics']['orders']]);
            fputcsv($file, []);

            // Top Products
            fputcsv($file, ['EN ÇOK SATAN ÜRÜNLER']);
            fputcsv($file, ['Ürün', 'Kategori', 'Satış', 'Gelir']);
            foreach ($data['topProducts'] as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->category,
                    $product->sales,
                    $product->revenue
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPDF($data, $period)
    {
        // Simplified PDF export (would use a library like DOMPDF in production)
        $html = '<h1>Satış Raporu</h1>';
        $html .= '<p>Dönem: ' . $period['start']->format('d/m/Y') . ' - ' . $period['end']->format('d/m/Y') . '</p>';
        $html .= '<h2>Toplam Gelir: ₺' . number_format($data['metrics']['revenue'], 2) . '</h2>';
        $html .= '<h2>Toplam Sipariş: ' . $data['metrics']['orders'] . '</h2>';

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="rapor-' . date('Y-m-d') . '.html"'
        ]);
    }
}
