namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PDF;
use ZipArchive;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\ExportLog;

class BulkExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $vendorIds;
    protected array $filters;
    protected int $adminId;
    protected string $adminEmail;
    protected string $ip;

    public function __construct(array $vendorIds, array $filters, int $adminId, string $adminEmail, string $ip)
    {
        $this->vendorIds = $vendorIds;
        $this->filters = $filters;
        $this->adminId = $adminId;
        $this->adminEmail = $adminEmail;
        $this->ip = $ip;
    }

    public function handle()
    {
        $exportDir = storage_path('app/exports/');
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0775, true);
        }

        $zipPath = $exportDir . 'bulk_export.zip';
        $zip = new ZipArchive;
        if (!$zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            throw new \Exception('ZIP dosyası oluşturulamadı.');
        }

        foreach ($this->vendorIds as $vendorId) {
            $vendor = Vendor::find($vendorId);
            if (!$vendor) continue;

            $items = $this->getFilteredData($vendor);

            $branding = [
                'name'    => $vendor->name ?? 'Vendor',
                'logo'    => asset($vendor->logo_path ?? 'images/default-logo.png'),
                'footer'  => data_get($vendor->settings, 'footer_note', ''),
                'columns' => data_get($vendor->settings, 'export_columns', ['name', 'quantity']),
                'color'   => data_get($vendor->settings, 'theme_color', '#333'),
            ];

            $pdf = PDF::loadView('export.pdf', compact('items', 'branding'));
            $filename = "rapor_{$vendor->slug}.pdf";
            $pdfPath = $exportDir . $filename;
            $pdf->save($pdfPath);

            $zip->addFile($pdfPath, $filename);

            ExportLog::create([
                'admin_id'    => $this->adminId,
                'vendor_id'   => $vendor->id,
                'vendor_name' => $vendor->name,
                'action'      => 'zip_export_batch',
                'file_name'   => $filename,
                'user_email'  => $this->adminEmail,
                'ip'          => $this->ip
            ]);
        }

        $zip->close();
    }

    protected function getFilteredData(Vendor $vendor)
    {
        return Order::query()
            ->where('vendor_id', $vendor->id)
            ->when(isset($this->filters['date_from']), fn($q) =>
                $q->whereDate('created_at', '>=', $this->filters['date_from']))
            ->when(isset($this->filters['date_to']), fn($q) =>
                $q->whereDate('created_at', '<=', $this->filters['date_to']))
            ->get();
    }
}