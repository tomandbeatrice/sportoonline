namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\Order;
use App\Models\ExportLog;

class ExportController extends Controller
{
    public function download(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;

        if (!$vendor) {
            abort(403, 'Vendor bilgisi alınamadı.');
        }

        // Vendor bazlı branding verisi
        $branding = [
            'name'    => $vendor->name ?? 'Bilinmeyen Vendor',
            'logo'    => asset($vendor->logo_path ?? 'images/default-logo.png'),
            'footer'  => data_get($vendor->settings, 'footer_note', ''),
            'columns' => data_get($vendor->settings, 'export_columns', ['name', 'quantity', 'price']),
            'color'   => data_get($vendor->settings, 'theme_color', '#222'),
        ];

        // Vendor'a özel filtreli veri çekimi
        $items = $this->getFilteredData($vendor, $request);

        // PDF oluşturma
        $pdf = PDF::loadView('export.pdf', compact('items', 'branding'))
            ->download("rapor_{$branding['name']}.pdf");

        // Export loglama
        ExportLog::create([
            'admin_id'    => $user->id,
            'vendor_id'   => $vendor->id,
            'vendor_name' => $vendor->name,
            'action'      => 'pdf_token_download',
            'file_name'   => "export_{$vendor->slug}.pdf",
            'user_email'  => $user->email,
            'ip'          => $request->ip()
        ]);

        return $pdf;
    }

    protected function getFilteredData($vendor, Request $request)
    {
        return Order::query()
            ->where('vendor_id', $vendor->id)
            ->when($request->filled('date_from'), fn($q) =>
                $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) =>
                $q->whereDate('created_at', '<=', $request->date_to))
            ->get();
    }
}