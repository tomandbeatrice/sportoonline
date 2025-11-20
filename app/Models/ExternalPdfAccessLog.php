use App\Models\Vendor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VendorStatsExport;
use PDF;

public function export(Request $request)
{
    $format = $request->input('format', 'xlsx');

    $vendors = Vendor::with(['externalPdfAccessLogs'])
        ->get()
        ->map(function ($vendor) {
            $logs = $vendor->externalPdfAccessLogs;

            return [
                'Vendor' => $vendor->name,
                'Token' => $vendor->token,
                'Durum' => $vendor->is_active ? 'Aktif' : 'Pasif',
                'Toplam Erişim' => $logs->count(),
                'Son Erişim' => optional($logs->max('accessed_at'))->format('d.m.Y H:i'),
                'En Çok Erişilen Dosya' => $logs
                    ->groupBy('file_name')
                    ->sortByDesc(fn($g) => $g->count())
                    ->keys()
                    ->first(),
            ];
        });

    if ($format === 'pdf') {
        $pdf = PDF::loadView('admin.exports.vendor_stats_excel', ['vendors' => $vendors]);
        return $pdf->download('vendor_stats.pdf');
    }

    return Excel::download(new VendorStatsExport($vendors), "vendor_stats.{$format}");
}