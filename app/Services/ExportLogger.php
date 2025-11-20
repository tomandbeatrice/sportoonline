<?php

namespace App\Services;

use App\Models\Vendor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ExportLogger
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = storage_path('logs/exports');
    }

    public function logSuccess(Vendor $vendor, string $zipPath): void
    {
        $logData = [
            'status'     => 'success',
            'vendor'     => $vendor->slug,
            'uuid'       => uniqid(),
            'file'       => basename($zipPath),
            'size'       => File::size($zipPath),
            'ip'         => request()->ip(),
            'timestamp'  => now()->toDateTimeString(),
        ];

        $this->writeLog($vendor->slug, $logData);
    }

    public function logFailure(Vendor $vendor, string $error): void
    {
        $logData = [
            'status'     => 'failure',
            'vendor'     => $vendor->slug,
            'uuid'       => uniqid(),
            'error'      => $error,
            'ip'         => request()->ip(),
            'timestamp'  => now()->toDateTimeString(),
        ];

        $this->writeLog($vendor->slug, $logData);
    }

    protected function writeLog(string $vendorSlug, array $data): void
    {
        $logDir = "{$this->basePath}/{$vendorSlug}/" . now()->format('Y-m-d');
        File::ensureDirectoryExists($logDir);

        $filename = "{$logDir}/export_" . now()->format('H-i-s') . ".json";
        File::put($filename, json_encode($data, JSON_PRETTY_PRINT));

        Log::info("Export log yazıldı: {$filename}");
    }
}