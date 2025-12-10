<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use ZipArchive;

class BulkInvoiceExportJob implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $folderPath = storage_path('app/exports/' . now()->format('Y/m/d'));
        $zipFileName = 'bulk_invoices_' . now()->format('Ymd_His') . '.zip';

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0775, true);
        }

        $zipFullPath = $folderPath . '/' . $zipFileName;
        $zip = new ZipArchive;

        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $invoiceFiles = Storage::files('invoices');

            foreach ($invoiceFiles as $file) {
                $filePath = storage_path('app/' . $file);
                $zip->addFile($filePath, basename($file));
            }

            $zip->close();
            // 🔔 İsteğe bağlı: veritabanına log atabiliriz
        }
    }
}