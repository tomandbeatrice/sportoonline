<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class ExternalPdfController extends Controller
{
    /**
     * Güvenli PDF görüntüleme endpoint'i
     */
    public function view(Request $request)
    {
        $file = $request->query('file');
        $token = $request->query('token');
        $vendorId = $request->query('vendor');
        $timestamp = $request->query('ts');

        // 🔐 Token doğrulama
        if ($token !== config('app.vendor_pdf_token')) {
            Log::channel('vendor')->warning('Yetkisiz PDF erişim denemesi', compact('file', 'token', 'vendorId'));
            return response()->json(['message' => 'Yetkisiz erişim'], Response::HTTP_FORBIDDEN);
        }

        // ⏳ Süre kontrolü
        $now = now()->timestamp;
        $expire = config('app.vendor_pdf_expire_seconds', 600);
        if (abs($now - intval($timestamp)) > $expire) {
            Log::channel('vendor')->warning('PDF erişim süresi dolmuş', compact('file', 'vendorId', 'timestamp'));
            return response()->json(['message' => 'Link süresi dolmuş'], Response::HTTP_FORBIDDEN);
        }

        // 📁 Dosya kontrolü
        $path = "external-pdf/{$file}";
        if (!Storage::disk('public')->exists($path)) {
            Log::channel('vendor')->error('PDF dosyası bulunamadı', compact('file', 'vendorId'));
            return response()->json(['message' => 'Dosya bulunamadı'], Response::HTTP_NOT_FOUND);
        }

        // 📊 Log yazımı
        Log::channel('vendor')->info('PDF görüntülendi', [
            'vendor_id' => $vendorId,
            'file' => $file,
            'timestamp' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // 📄 PDF response
        return response()->file(
            Storage::disk('public')->path($path),
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Vendor bazlı zip export endpoint'i
     */
    public function exportZip(Request $request)
    {
        $vendorId = $request->query('vendor');
        $token = $request->query('token');

        // 🔐 Token doğrulama
        if ($token !== config('app.vendor_pdf_token')) {
            Log::channel('vendor')->warning('Yetkisiz zip export denemesi', compact('vendorId'));
            return response()->json(['message' => 'Yetkisiz erişim'], Response::HTTP_FORBIDDEN);
        }

        // 📁 Vendor klasörü kontrolü
        $folder = "external-pdf/vendor{$vendorId}";
        $files = Storage::disk('public')->files($folder);

        if (empty($files)) {
            return response()->json(['message' => 'Hiçbir dosya bulunamadı'], Response::HTTP_NOT_FOUND);
        }

        // 📦 Geçici zip dosyası oluştur
        $zipPath = storage_path("app/temp/vendor{$vendorId}-reports.zip");
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                $fullPath = Storage::disk('public')->path($file);
                $zip->addFile($fullPath, basename($file));
            }
            $zip->close();
        } else {
            return response()->json(['message' => 'Zip oluşturulamadı'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // 📊 Log yazımı
        Log::channel('vendor')->info('Vendor zip export yapıldı', [
            'vendor_id' => $vendorId,
            'file_count' => count($files),
            'timestamp' => now()->toDateTimeString(),
            'ip' => $request->ip(),
        ]);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}