<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\ExportLogService;
use App\Services\ExportCleanupService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class BrandingExportController extends Controller
{
    public function export()
    {
        $vendors = Vendor::select('id', 'name', 'branding_color', 'branding_font', 'branding_logo')->get();

        $data = $vendors->map(function ($vendor) {
            return [
                'Vendor ID' => $vendor->id,
                'Vendor Name' => $vendor->name,
                'Color' => $vendor->branding_color,
                'Font' => $vendor->branding_font,
                'Logo URL' => asset('storage/' . $vendor->branding_logo),
            ];
        });

        $filename = 'vendor_branding_' . now()->format('Ymd_His') . '.xlsx';

        // ✅ Export işlemi
        $export = new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $data;
            public function __construct(Collection $data) { $this->data = $data; }
            public function collection() { return $this->data; }
            public function headings(): array {
                return ['Vendor ID', 'Vendor Name', 'Color', 'Font', 'Logo URL'];
            }
        };

        // ✅ Meta loglama
        ExportLogService::logBrandingExport($filename, $vendors->count());

        return Excel::download($export, $filename);
    }

    public function weeklyCleanup()
    {
        return ExportCleanupService::generateWeeklyCleanup();
    }
}