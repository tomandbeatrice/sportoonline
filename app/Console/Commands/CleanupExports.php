<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupExports extends Command
{
    protected $signature = 'exports:cleanup';
    protected $description = 'Eski export dosyalarını siler ve haftalık log oluşturur';

    public function handle()
    {
        $now = now();
        $deleted = [];

        foreach (Storage::allFiles('exports') as $file) {
            $path = storage_path("app/{$file}");

            if (!File::exists($path)) {
                continue;
            }

            $lastModified = File::lastModified($path);
            $ageInDays = $now->diffInDays(Carbon::createFromTimestamp($lastModified));

            if ($ageInDays <= 3) {
                continue;
            }

            $sizeKb = round(File::size($path) / 1024, 2);
            Storage::delete($file);

            $deleted[] = [
                'file' => $file,
                'size_kb' => $sizeKb,
                'age_days' => $ageInDays,
                'deleted_at' => $now->toDateTimeString(),
            ];
        }

        if ($deleted) {
            $week = $now->format('W');
            $year = $now->year;
            $logFile = "exports/logs/cleanup_{$year}_W{$week}.json";

            try {
                $sizeArray = array_column($deleted, 'size_kb');
                $totalSizeMb = $sizeArray ? round(array_sum($sizeArray) / 1024, 2) : 0;

                $json = json_encode([
                    'last_cleanup' => $now->toDateTimeString(),
                    'deleted_count' => count($deleted),
                    'total_size_mb' => $totalSizeMb,
                    'files' => $deleted,
                ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

                Storage::makeDirectory('exports/logs'); // klasör yoksa oluştur
                Storage::put($logFile, $json);
            } catch (\JsonException $e) {
                Log::error('Temizlik logu JSON encode hatası: ' . $e->getMessage());
                $this->error('JSON encode başarısız: ' . $e->getMessage());
                return;
            }
        }

        $this->info('Export temizlik tamamlandı. Silinen dosya sayısı: ' . count($deleted));
    }
}