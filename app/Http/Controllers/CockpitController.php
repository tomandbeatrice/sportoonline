<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class CockpitController extends Controller
{
    public function logAssetPerformance(Request $request)
    {
        foreach ($request->assets as $asset) {
            $duration = (int) $asset['duration'];

            // Ana log
            Log::channel('cockpit')->info('Asset Loaded', [
                'name' => $asset['name'],
                'duration_ms' => $duration,
                'size' => $asset['size'],
                'timestamp' => now()->toIso8601String()
            ]);

            // ⚠️ Uyarı logu (1000ms üzeri)
            if ($duration > 1000) {
                Log::channel('cockpit_alert')->warning('Asset Slow Load Detected', [
                    'name' => $asset['name'],
                    'duration_ms' => $duration,
                    'size' => $asset['size'],
                    'timestamp' => now()->toIso8601String()
                ]);
            }
        }

        return response()->json(['status' => 'logged']);
    }

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

    public function exportAssetPerformanceCsv()
    {
        $lines = file(storage_path('logs/cockpit.log'));
        $rows = [["Dosya", "Boyut", "Yüklenme Süresi (ms)", "Zaman"]];

        foreach ($lines as $line) {
            if (str_contains($line, 'Asset Loaded')) {
                preg_match('/name":"(.*?)".*?duration_ms":(\d+).*?size":"(.*?)".*?timestamp":"(.*?)"/', $line, $matches);
                if (count($matches) === 5) {
                    $rows[] = [
                        $matches[1],
                        $matches[3],
                        $matches[2],
                        $matches[4],
                    ];
                }
            }
        }

        $csv = implode("\n", array_map(fn($r) => implode(',', $r), $rows));

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="asset-performance.csv"',
        ]);
    }
}