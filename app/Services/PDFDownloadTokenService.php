<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class PDFDownloadTokenService
{
    /**
     * Token üretir ve vendor_id ile birlikte cache'e kaydeder.
     *
     * @param int $vendorId
     * @param int $expiresIn (saniye cinsinden)
     * @return string
     */
    public function generate($vendorId, $expiresIn = 300)
    {
        $token = Str::random(32);
        $key = "pdf_token_{$token}";
        $payload = ['vendor_id' => $vendorId];

        Cache::put($key, $payload, $expiresIn);
        return $token;
    }

    /**
     * Token'ı doğrular ve payload'ı döner.
     *
     * @param string $token
     * @return array|null
     */
    public function validate($token)
    {
        $key = "pdf_token_{$token}";
        return Cache::get($key);
    }

    /**
     * Token'ı geçersiz kılar (tek kullanımlık mantık için).
     *
     * @param string $token
     * @return void
     */
    public function invalidate($token)
    {
        Cache::forget("pdf_token_{$token}");
    }
}