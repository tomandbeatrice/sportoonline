<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TurboCompetitionService;

class FinalizeTurboCompetition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'turbo:finalize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finalize ended Turbo competitions and distribute rewards';

    protected $turboService;

    public function __construct(TurboCompetitionService $turboService)
    {
        parent::__construct();
        $this->turboService = $turboService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('🏁 Turbo Mod yarışmaları kontrol ediliyor...');

        try {
            $this->turboService->checkAndFinalizeEndedCompetitions();
            
            $this->info('✅ Turbo Mod yarışmaları başarıyla finalize edildi!');
            $this->info('🎁 Ödüller dağıtıldı ve yeni dönem başlatıldı.');
        } catch (\Exception $e) {
            $this->error('❌ Hata: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
