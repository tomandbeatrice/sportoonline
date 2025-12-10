<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemBrainController extends Controller
{
    /**
     * Get system status and health metrics
     */
    public function status()
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'debug' => config('app.debug'),
            'cache' => [
                'driver' => config('cache.default'),
                'status' => $this->checkCacheStatus()
            ],
            'database' => [
                'connection' => config('database.default'),
                'status' => $this->checkDatabaseStatus()
            ],
            'queue' => [
                'driver' => config('queue.default'),
                'status' => 'operational'
            ],
            'storage' => [
                'disk' => config('filesystems.default'),
                'status' => 'operational'
            ]
        ];

        return response()->json($status);
    }

    /**
     * Check cache status
     */
    private function checkCacheStatus(): string
    {
        try {
            Cache::put('health_check', true, 10);
            Cache::forget('health_check');
            return 'operational';
        } catch (\Exception $e) {
            return 'error: ' . $e->getMessage();
        }
    }

    /**
     * Check database status
     */
    private function checkDatabaseStatus(): string
    {
        try {
            \DB::connection()->getPdo();
            return 'connected';
        } catch (\Exception $e) {
            return 'error: ' . $e->getMessage();
        }
    }
}
