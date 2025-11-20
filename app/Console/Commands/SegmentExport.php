<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SegmentExportSheet;

class SegmentExport extends Command
{
    protected $signature = 'segment:export {segmentId}';
    protected $description = 'Belirli segment için Excel export oluşturur';

    public function handle()
    {
        $segmentId = $this->argument('segmentId');
        $filename = "segment_export_{$segmentId}_" . now()->format('Ymd_His') . ".xlsx";
        $path = "exports/segments/{$filename}";

        Excel::store(new SegmentExportSheet($segmentId), $path, 'local');

        $this->info("📊 Segment #{$segmentId} export oluşturuldu: {$filename}");
        Log::info("[SegmentExport] Segment #{$segmentId} export oluşturuldu: {$filename}");

        return $filename; // Controller tarafından Artisan::output() ile alınabilir
    }
}