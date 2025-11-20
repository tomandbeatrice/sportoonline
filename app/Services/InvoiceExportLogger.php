<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvoiceExportLogger
{
    /**
     * Veritabanına log kaydı düşer.
     *
     * @param int $adminId
     * @param int $vendorId
     * @param string $action
     * @return void
     */
    public function logDatabase(int $adminId, int $vendorId, string $action): void
    {
        DB::table('export_logs')->insert([
            'admin_id'   => $adminId,
            'vendor_id'  => $vendorId,
            'action'     => $action,
            'created_at' => now(),
        ]);
    }

    /**
     * Dosya loguna export işlemi kaydeder.
     *
     * @param string $filename
     * @param string $userEmail
     * @return void
     */
    public function logExport(string $filename, string $userEmail): void
    {
        Log::channel('exports')->info('Export işlemi', [
            'filename'  => $filename,
            'user'      => $userEmail,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Dosya loguna download işlemi kaydeder.
     *
     * @param string $filename
     * @param string $userEmail
     * @return void
     */
    public function logDownload(string $filename, string $userEmail): void
    {
        Log::channel('exports')->info('Download işlemi', [
            'filename'  => $filename,
            'user'      => $userEmail,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * export.log dosyasından vendor bazlı özet çıkarır.
     *
     * @return array
     */
    public function getSummary(): array
    {
        $logPath = storage_path('logs/export.log');
        if (!file_exists($logPath)) return [];

        $lines = file($logPath);
        $summary = [];

        foreach ($lines as $line) {
            if (Str::contains($line, 'Export işlemi')) {
                preg_match('/filename":"([^"]+\.pdf)"/', $line, $matches);
                $file = $matches[1] ?? null;
                $vendor = explode('_', $file)[0] ?? 'isimsiz';

                $summary[$vendor] = ($summary[$vendor] ?? 0) + 1;
            }
        }

        return $summary;
    }
}