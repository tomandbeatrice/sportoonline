<?php

namespace App\Services;

use App\Models\ExportLog;

class ExportLogService
{
    public static function logBrandingExport(string $filename, int $vendorCount): void
    {
        ExportLog::create([
            'filename' => $filename,
            'vendor_count' => $vendorCount,
            'export_type' => 'branding',
            'exported_at' => now(),
        ]);
    }
}