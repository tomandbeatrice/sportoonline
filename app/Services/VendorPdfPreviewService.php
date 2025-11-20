<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VendorPdfPreviewService
{
    protected $secretKey = 'vendor_pdf_preview'; // sabit prefix

    /**
     * Token üretimi – vendorId ve dosya adı şifrelenir
     */
    public function generateToken(string $vendorId, string $fileName): string
    {
        $payload = [
            'vendor' => $vendorId,
            'file' => $fileName,
            'expires_at' => Carbon::now()->addMinutes(30)->timestamp,
            'nonce' => Str::random(8),
        ];

        return Crypt::encryptString(json_encode($payload));
    }

    /**
     * Token çözümleme ve doğrulama
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = json_decode(Crypt::decryptString($token), true);

            if (!isset($decoded['vendor'], $decoded['file'], $decoded['expires_at'])) {
                return null;
            }

            if (Carbon::now()->timestamp > $decoded['expires_at']) {
                return null;
            }

            $path = "vendors/{$decoded['vendor']}/pdf/{$decoded['file']}";
            if (!Storage::exists($path)) {
                return null;
            }

            return [
                'vendor' => $decoded['vendor'],
                'file' => $decoded['file'],
                'path' => $path,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * PDF içeriğini döndür (view için)
     */
    public function getPdfContent(string $path): string
    {
        return Storage::path($path);
    }
}