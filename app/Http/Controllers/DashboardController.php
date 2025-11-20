<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Events\DashboardMetricsUpdated;

class DashboardController extends Controller
{
    /**
     * Real-time dashboard metrics
     */
    public function getRealtimeMetrics()
    {
        $metrics = [
            'total_orders' => DB::table('orders')->count(),
            'today_revenue' => DB::table('orders')
                ->whereDate('created_at', today())
                ->sum('total_price'),
            'active_campaigns' => DB::table('incentive_campaigns')
                ->where('is_active', true)
                ->count(),
            'pending_exports' => DB::table('export_logs')
                ->where('status', 'pending')
                ->count(),
            'conversion_rate' => $this->calculateConversionRate(),
            'avg_order_value' => DB::table('orders')
                ->whereDate('created_at', today())
                ->avg('total_price'),
        ];

        // Broadcast to dashboard
        event(new DashboardMetricsUpdated($metrics, 'admin'));

        return response()->json($metrics);
    }

    /**
     * Get module logs
     */
    public function getModuleLogs()
    {
        $modules = Cache::remember('dashboard.module_logs', 60, function () {
            return [
                [
                    "module" => "export",
                    "progress" => DB::table('export_logs')
                        ->where('status', 'completed')
                        ->count() * 100 / max(DB::table('export_logs')->count(), 1),
                    "status" => "active",
                    "duration" => $this->calculateModuleDuration('export'),
                    "errorCount" => DB::table('export_logs')->where('status', 'failed')->count(),
                    "lastAction" => DB::table('export_logs')->latest()->value('action') ?? "N/A",
                    "data" => $this->getModuleTrendData('export')
                ],
                [
                    "module" => "campaign",
                    "progress" => DB::table('campaigns')
                        ->where('status', 'active')
                        ->count() * 100 / max(DB::table('campaigns')->count(), 1),
                    "status" => "active",
                    "duration" => $this->calculateModuleDuration('campaign'),
                    "errorCount" => 0,
                    "lastAction" => "Kampanya güncellendi",
                    "data" => $this->getModuleTrendData('campaign')
                ]
            ];
        });

        return response()->json($modules);
    }

    /**
     * Calculate conversion rate
     */
    private function calculateConversionRate(): float
    {
        $visitors = Cache::get('daily_visitors', 100);
        $orders = DB::table('orders')->whereDate('created_at', today())->count();
        
        return $visitors > 0 ? round(($orders / $visitors) * 100, 2) : 0;
    }

    /**
     * Calculate module duration
     */
    private function calculateModuleDuration(string $module): string
    {
        $seconds = rand(60, 300); // Mock data
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d', $minutes, $secs);
    }

    /**
     * Get marketplace public stats
     */
    public function marketplaceStats()
    {
        $stats = Cache::remember('marketplace.stats', 300, function () {
            return [
                'total_products' => DB::table('products')->where('status', 'active')->count(),
                'total_sellers' => DB::table('users')->where('role', 'seller')->count(),
                'total_orders' => DB::table('orders')->count(),
                'total_customers' => DB::table('users')->where('role', 'buyer')->count(),
            ];
        });

        return response()->json($stats);
    }

    /**
     * Get module trend data
     */
    private function getModuleTrendData(string $module): array
    {
        return [
            ["timestamp" => now()->subMinutes(10)->toDateTimeString(), "count" => rand(2, 5)],
            ["timestamp" => now()->subMinutes(5)->toDateTimeString(), "count" => rand(3, 7)],
            ["timestamp" => now()->toDateTimeString(), "count" => rand(4, 8)],
        ];
    }
}
