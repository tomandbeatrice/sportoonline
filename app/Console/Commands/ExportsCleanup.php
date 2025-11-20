<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportsCleanup extends Command
{
    protected $signature = 'exports:cleanup';
    protected $description = 'Delete old export files and log the cleanup';

    public function handle()
    {
        $disk = Storage::disk('local');
        $exportPath = 'exports';
        $logPath = $exportPath . '/cleanup_logs';

        // Klasör yoksa oluştur
        if (!$disk->exists($logPath)) {
            $disk->makeDirectory($logPath);
        }

        $deleted = [];

        foreach ($disk->allFiles($exportPath) as $file) {
            // cleanup_logs klasörünü atla
            if (str_starts_with($file, $logPath)) {
                continue;
            }

            $lastModified = $disk->lastModified($file);
            if ($lastModified < now()->subDays(7)->timestamp) {
                $disk->delete($file);
                $deleted[] = [
                    'file' => $file,
                    'deleted_at' => now()->toDateTimeString(),
                ];
            }
        }

        if (!empty($deleted)) {
            $logFileName = 'cleanup_log_' . now()->format('Y_m_d_His') . '.json';
            $disk->put($logPath . '/' . $logFileName, json_encode($deleted, JSON_PRETTY_PRINT));

            $this->info(count($deleted) . ' file(s) deleted. Log saved to ' . $logFileName);
        } else {
            $this->info('No files deleted. All exports are recent.');
        }
    }
}