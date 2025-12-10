<?php

namespace App\Services;

use App\Models\ExportLog;
use Exception;
use Illuminate\Support\Facades\Log;

class ExportService
{
    public static function run(): bool
    {
        try {
            // ⏱ Başlangıç zamanı
            $startTime = now();

            // 📦 Export işlemi (örnek veri)
            $exportedData = self::generateExport();

            // ✅ Başarılı log
            self::log('Export başarıyla tamamlandı', 'başarı', [
                'count' => count($exportedData),
                'duration' => now()->diffInSeconds($startTime)
            ]);

            return true;
        } catch (Exception $e) {
            // ❌ Hata logu
            self::log('Export sırasında hata oluştu: ' . $e->getMessage(), 'hata');

            Log::error('ExportService error', ['exception' => $e]);
            return false;
        }
    }

    private static function generateExport(): array
    {
        // 🔧 Gerçek export işlemi burada yapılır
        return [
            ['id' => 1, 'name' => 'Ürün A'],
            ['id' => 2, 'name' => 'Ürün B']
        ];
    }

    private static function log(string $message, string $type, array $meta = []): void
    {
        ExportLog::create([
            'message' => $message,
            'type' => $type,
            'meta' => json_encode($meta)
        ]);
    }
}