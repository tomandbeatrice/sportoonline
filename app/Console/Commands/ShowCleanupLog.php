<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ShowCleanupLog extends Command
{
    protected $signature = 'exports:log:show';
    protected $description = 'Show the latest cleanup log file content';

    public function handle()
    {
        $logPath = 'exports/logs/cleanup_log.json';

        if (!Storage::exists($logPath)) {
            $this->error('Log dosyası bulunamadı: ' . $logPath);
            return;
        }

        $content = Storage::get($logPath);
        $data = json_decode($content, true);

        if (!$data) {
            $this->error('Log dosyası okunamadı veya bozuk.');
            return;
        }

        $this->info('🧹 Son Temizlik Özeti');
        $this->line('Tarih: ' . $data['last_cleanup']);
        $this->line('Silinen Dosya Sayısı: ' . $data['deleted_count']);
        $this->line('Toplam Boyut: ' . $data['total_size_mb'] . ' MB');

        foreach ($data['files'] as $file) {
            $this->line("- {$file['file']} ({$file['size_kb']} KB, {$file['age_days']} gün önce silindi)");
        }
    }
}