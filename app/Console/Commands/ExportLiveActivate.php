<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExportService;

class ExportLiveActivate extends Command
{
    protected $signature = 'exports:activate';
    protected $description = 'Tüm vendorlar için export işlemini başlatır ve loglar';

    public function handle()
    {
        $this->info('Export işlemi başlatılıyor...');
        app(ExportService::class)->runForAllVendors();
        $this->info('Export işlemi tamamlandı.');
    }
}