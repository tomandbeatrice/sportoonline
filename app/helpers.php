<?php

use Illuminate\Support\Str;

if (!function_exists('generateExportZipPath')) {
    function generateExportZipPath($vendorName = 'genel', $invoiceCount = 0)
    {
        $slug     = Str::slug($vendorName);
        $date     = now()->format('Ymd_His');
        $fileName = "{$slug}_{$date}_{$invoiceCount}adet.zip";

        return "exports/{$fileName}";
    }
}