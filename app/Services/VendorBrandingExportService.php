<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VendorBrandingExport;
use Illuminate\Support\Facades\Log;
use App\Models\ExportLog;

class VendorBrandingExportService
{
    public function export(): string
    {
        $fileName = 'vendor_branding_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = 'exports/' . $fileName;

        Excel::store(new VendorBrandingExport, $filePath, 'local');

        // Laravel log dosyasına yaz
        Log::info('Vendor branding exported', [
            'file'      => $filePath,
            'user_id'   => auth()->id() ?? request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        // Veritabanına yaz
        ExportLog::create([
            'admin_id'     => auth()->user()?->isAdmin() ? auth()->id() : null,
            'vendor_id'    => auth()->user()?->vendor_id ?? null,
            'vendor_name'  => auth()->user()?->vendor->name ?? null,
            'user_email'   => auth()->user()?->email ?? null,
            'action'       => 'vendor_branding_export',
            'file_name'    => $fileName,
            'ip'           => request()->ip(),
            'created_at'   => now(),
        ]);

        return storage_path($filePath);
    }
}