<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ExportCleanup extends Command
{
    protected $signature = 'export:cleanup';
    protected $description = 'Export klasöründeki eski dosyaları temizler';

    public function handle()
    {
        $path = storage_path('app/exports');
        $threshold = now()->subDays(3);

        if (!File::exists($path)) {
            $this->warn('Export klasörü bulunamadı.');
            return;
        }

        $deleted = 0;

        foreach (File::files($path) as $file) {
            if ($file->getCTime() < $threshold->timestamp) {
                File::delete($file->getRealPath());
                $deleted++;
            }
        }

        $this->info("🧹 $deleted dosya temizlendi.");
        Log::info("[ExportCleanup] $deleted dosya temizlendi.");
    }
}