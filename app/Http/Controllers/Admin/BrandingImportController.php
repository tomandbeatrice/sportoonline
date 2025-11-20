<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorBranding;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BrandingImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $path = $request->file('file')->storeAs('temp', 'branding_import_' . time() . '.xlsx');
            $rows = Excel::toArray([], storage_path('app/' . $path))[0];
            
            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                // Header satırını atla
                if ($index === 0) continue;

                $vendorId = $row[0] ?? null;
                $logoUrl = $row[4] ?? null;
                $primaryColor = $row[2] ?? null;
                $secondaryColor = $row[3] ?? null;
                $fontFamily = $row[3] ?? null;

                if (!$vendorId) {
                    $errors[] = "Satır " . ($index + 1) . ": Vendor ID eksik";
                    continue;
                }

                $vendor = Vendor::find($vendorId);
                if (!$vendor) {
                    $errors[] = "Satır " . ($index + 1) . ": Vendor bulunamadı (ID: {$vendorId})";
                    continue;
                }

                // Branding güncelle
                VendorBranding::updateOrCreate(
                    ['vendor_id' => $vendorId],
                    [
                        'logo_url' => $logoUrl,
                        'primary_color' => $primaryColor,
                        'secondary_color' => $secondaryColor,
                        'font_family' => $fontFamily,
                    ]
                );

                $imported++;
            }

            // Temp dosyayı temizle
            Storage::delete($path);

            Log::info('Branding import tamamlandı', [
                'imported' => $imported,
                'errors_count' => count($errors),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$imported} vendor branding güncellendi",
                'errors' => $errors,
            ]);

        } catch (\Throwable $e) {
            Log::error('Branding import hatası', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Import işlemi başarısız: ' . $e->getMessage(),
            ], 500);
        }
    }
}
