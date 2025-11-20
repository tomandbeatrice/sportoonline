<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanOldExports extends Command
{
    protected $signature = 'exports:clean';
    protected $description = '3 günden eski export dosyalarını siler';

    public function handle()
    {
        $files = Storage::files('exports');
        $now = now();

        foreach ($files as $file) {
            $lastModified = Storage::lastModified($file);
            $ageInDays = $now->diffInDays(\Carbon\Carbon::createFromTimestamp($lastModified));

            if ($ageInDays > 3) {
                Storage::delete($file);
                Log::info("Export dosyası silindi: {$file}");
                $this->info("Silindi: {$file}");
            }
        }

        $this->info('Export temizlik tamamlandı.');
    }
}