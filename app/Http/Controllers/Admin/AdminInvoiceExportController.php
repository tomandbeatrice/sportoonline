<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\InvoiceExportLog;
use ZipArchive;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\InvoiceExportLogger;

class AdminInvoiceExportController extends Controller
{
    public function exportForm()
    {
        $admins = Admin::all();
        $vendors = Vendor::all();
        return view('admin.invoices.export', compact('admins', 'vendors'));
    }

    public function exportLogs(Request $request)
    {
        $query = InvoiceExportLog::with('vendor');

        if ($request->vendor_id) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->start_date) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date));
        }

        if ($request->end_date) {
            $query->where('created_at', '<=', Carbon::parse($request->end_date));
        }

        $logs = $query->latest()->paginate(20)->through(function ($log) {
            $tokenFile = collect(Storage::files('exports/invoices/tokens'))
                ->first(fn($file) => Storage::get($file) === $log->path);

            $log->download_token = $tokenFile ? basename($tokenFile) : null;
            $log->size_kb = Storage::exists($log->path) ? round(Storage::size($log->path) / 1024) : null;

            return $log;
        });

        $vendors = Vendor::all();

        return view('admin.invoices.logs', compact('logs', 'vendors'));
    }

    public function bulkDownload(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $adminId  = $request->admin_id;
        $vendorId = $request->vendor_id;
        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        $query = Invoice::query();

        if ($adminId)  $query->where('admin_id', $adminId);
        if ($vendorId) $query->where('vendor_id', $vendorId);

        $query->whereBetween('created_at', [$startDate, $endDate]);
        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            return back()->with('toast_error', 'Bu filtreye ait fatura bulunamadı.');
        }

        $pdfCount = $invoices->count();
        $vendor = Vendor::find($vendorId);
        $vendorSlug = Str::slug($vendor->name ?? 'genel');

        $dir = "exports/invoices/bulk/{$vendorSlug}/" . now()->format('Ymd_His');
        Storage::makeDirectory($dir);

        $zipName = "TopluFaturaExport_{$startDate->format('dmY')}_{$endDate->format('dmY')}.zip";
        $zipPathRelative = "{$dir}/{$zipName}";
        $zipPathAbsolute = storage_path("app/{$zipPathRelative}");

        $zip = new ZipArchive;
        if ($zip->open($zipPathAbsolute, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            foreach ($invoices as $invoice) {
                $pdf = PDF::loadView('exports.invoice_pdf', compact('invoice'))->output();
                $zip->addFromString("Fatura_{$invoice->id}.pdf", $pdf);
            }
            $zip->close();
        }

        InvoiceExportLogger::log($zipPathRelative, $vendorId, $pdfCount, 'bulk');

        $downloadToken = Str::uuid();
        $tokenPath = "exports/invoices/tokens/{$downloadToken}";
        Storage::disk('local')->put($tokenPath, $zipPathRelative);

        return back()
            ->with('success', 'Export başarıyla tamamlandı.')
            ->with('download_token', $downloadToken);
    }

    public function downloadByToken($token)
    {
        $tokenPath = "exports/invoices/tokens/{$token}";

        if (!Storage::exists($tokenPath)) {
            return abort(404, 'Token geçersiz veya süresi dolmuş.');
        }

        $zipRelative = Storage::get($tokenPath);
        $zipAbsolute = storage_path("app/{$zipRelative}");

        Storage::delete($tokenPath);

        return response()->download($zipAbsolute)->deleteFileAfterSend(true);
    }
}