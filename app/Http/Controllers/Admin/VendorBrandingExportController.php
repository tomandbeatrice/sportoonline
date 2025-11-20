<?php

namespace App\Http\Controllers\Admin;

use App\Models\Vendor;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorBrandingExportController
{
    public function export(): Response
    {
        try {
            $vendors = Vendor::select('id', 'name', 'branding_color', 'branding_font', 'branding_logo')->get();

            if ($vendors->isEmpty()) {
                Log::warning('Export işlemi başarısız: vendor verisi boş.');
                return response()->json(['error' => 'Hiçbir vendor bulunamadı.'], 404);
            }

            $data = $vendors->map(fn($vendor) => [
                'Vendor ID'   => $vendor->id,
                'Vendor Name' => $vendor->name,
                'Color'       => $vendor->branding_color,
                'Font'        => $vendor->branding_font,
                'Logo URL'    => asset('storage/' . $vendor->branding_logo),
            ]);

            $relativePath = 'exports/vendor_branding.xlsx';
            $fullPath = storage_path('app/' . $relativePath);

            if (!Storage::exists('exports')) {
                Storage::makeDirectory('exports');
            }

            Excel::store(new class($data) implements FromCollection, WithHeadings {
                protected Collection $data;
                public function __construct(Collection $data) { $this->data = $data; }
                public function collection(): Collection { return $this->data; }
                public function headings(): array {
                    return ['Vendor ID', 'Vendor Name', 'Color', 'Font', 'Logo URL'];
                }
            }, $relativePath);

            return file_exists($fullPath)
                ? response()->download($fullPath)
                : response()->json(['error' => 'Export dosyası oluşturulamadı.'], 500);

        } catch (\Throwable $e) {
            Log::error('Export sırasında hata oluştu: ' . $e->getMessage());
            return response()->json(['error' => 'Export işlemi sırasında sistem hatası oluştu.'], 500);
        }
    }
}