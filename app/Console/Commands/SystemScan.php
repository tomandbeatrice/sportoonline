<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SystemScan extends Command
{
    protected $signature = 'system:scan';
    protected $description = 'Kod yapısındaki eksik, hatalı veya uyumsuz dosyaları tarar';

    public function handle()
    {
        $this->info('🔍 SystemBrain taraması başlatıldı...');

        // Migration dosyaları kontrolü
        $migrations = File::files(database_path('migrations'));
        foreach ($migrations as $file) {
            if (!str_contains($file->getFilename(), '.php')) {
                $this->warn("Migration dosyası eksik veya hatalı: {$file->getFilename()}");
            }
        }

        // Controller sınıfı var mı?
        $controllers = File::allFiles(app_path('Http/Controllers'));
        foreach ($controllers as $controller) {
            $contents = File::get($controller->getRealPath());
            if (!str_contains($contents, 'class')) {
                $this->error("Controller sınıfı tanımsız: {$controller->getFilename()}");
            }
        }

        // Export klasörü kontrolü
        $exportPath = storage_path('app/exports');
        if (!File::exists($exportPath)) {
            $this->error("Export klasörü eksik: storage/app/exports");
        } else {
            $files = File::files($exportPath);
            if (count($files) === 0) {
                $this->warn("Export klasörü boş: storage/app/exports");
            }
        }

        // Log dosyası kontrolü
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            $lastLog = File::get($logPath);
            if (str_contains($lastLog, 'ERROR')) {
                $this->warn("Laravel log dosyasında hata bulundu.");
            }
        }

        $this->info('✅ SystemBrain taraması tamamlandı.');
    }
}