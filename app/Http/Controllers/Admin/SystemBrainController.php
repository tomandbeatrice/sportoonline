<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemBrainController extends Controller
{
    public function status()
    {
        $logPath = storage_path('logs/laravel.log');
        $lines = file_exists($logPath) ? file($logPath) : [];

        $logs = collect($lines)
            ->filter(fn($line) => str_contains($line, 'ERROR') || str_contains($line, 'WARNING') || str_contains($line, 'INFO'))
            ->take(-10)
            ->map(fn($line) => [
                'level' => str_contains($line, 'ERROR') ? '❌ ERROR' : (str_contains($line, 'WARNING') ? '⚠️ WARNING' : '✅ INFO'),
                'message' => trim(substr($line, 25)),
                'timestamp' => substr($line, 0, 19)
            ])
            ->values();

        $exports = collect($lines)
            ->filter(fn($line) => str_contains($line, '[SegmentExport]'))
            ->take(-5)
            ->map(function ($line) {
                preg_match('/Segment #(\d+) export oluşturuldu: (segment_export_\d+_\d+\.xlsx)/', $line, $matches);
                return [
                    'segmentId' => $matches[1] ?? null,
                    'filename' => $matches[2] ?? null,
                    'timestamp' => substr($line, 0, 19)
                ];
            })
            ->values();

        $commands = [
            ['name' => 'SystemScan', 'lastRun' => null],
            ['name' => 'ExportCleanup', 'lastRun' => null],
            ['name' => 'SegmentExport', 'lastRun' => $exports->last()['timestamp'] ?? null]
        ];

        return response()->json([
            'commands' => $commands,
            'logs' => $logs,
            'exports' => $exports
        ]);
    }
}
