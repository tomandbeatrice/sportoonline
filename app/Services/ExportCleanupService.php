<?php

namespace App\Services;

use App\Models\ExportLog;
use Illuminate\Support\Collection;
use App\Exports\BrandingCleanupExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportCleanupService
{
    public static function generateWeeklyCleanup(): BinaryFileResponse
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $logs = ExportLog::where('export_type', 'branding')
            ->whereBetween('exported_at', [$startOfWeek, $endOfWeek])
            ->get();

        $data = $logs->map(function ($log) {
            return [
                'Filename'     => $log->filename,
                'Vendor Count' => $log->vendor_count,
                'Exported At'  => $log->exported_at->format('Y-m-d H:i:s'),
            ];
        });

        $filename = 'branding_export_cleanup_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new BrandingCleanupExport($data), $filename);
    }
}