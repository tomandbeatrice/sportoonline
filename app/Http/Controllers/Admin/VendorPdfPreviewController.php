<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use App\Services\VendorPdfPreviewService;

class VendorPdfPreviewController extends Controller
{
    protected $service;

    public function __construct(VendorPdfPreviewService $service)
    {
        $this->service = $service;
    }

    /**
     * Tokenlı PDF preview endpoint
     */
    public function show(string $token)
    {
        $validated = $this->service->validateToken($token);

        if (!$validated) {
            Log::channel('vendor')->warning('Geçersiz veya süresi dolmuş token ile erişim denemesi', [
                'token' => $token,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json(['message' => 'Yetkisiz veya süresi dolmuş erişim'], 403);
        }

        // Loglama (isteğe bağlı)
        Log::channel('vendor')->info('PDF preview erişimi başarılı', [
            'vendor_id' => $validated['vendor'],
            'file' => $validated['file'],
            'timestamp' => now()->toDateTimeString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->file(
            $this->service->getPdfContent($validated['path']),
            ['Content-Type' => 'application/pdf']
        );
    }
}