<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CockpitController extends Controller
{
    /**
     * CDN asset yüklenme süresi loglanır.
     */
    public function logAssetPerformance(Request $request)
    {
        foreach ($request->assets as $asset) {
            Log::channel('cockpit')->info('Asset Loaded', [
                'name' => $asset['name'],
                'duration_ms' => (int) $asset['duration'],
                'size' => $asset['size'],
                'timestamp' => now()->toIso8601String()
            ]);
        }

        return response()->json(['status' => 'logged']);
    }

    /**
     * Cockpit ekranı için CDN asset performans loglarını getirir.
     */
    public function getAssetPerformance()
    {
        $lines = file(storage_path('logs/cockpit.log'));
        $logs = [];

        foreach ($lines as $line) {
            if (str_contains($line, 'Asset Loaded')) {
                preg_match('/name":"(.*?)".*?duration_ms":(\d+).*?size":"(.*?)".*?timestamp":"(.*?)"/', $line, $matches);
                if (count($matches) === 5) {
                    $logs[] = [
                        'name' => $matches[1],
                        'duration_ms' => (int) $matches[2],
                        'size' => $matches[3],
                        'timestamp' => $matches[4],
                    ];
                }
            }
        }

        return array_reverse(array_slice($logs, -100));
    }
}