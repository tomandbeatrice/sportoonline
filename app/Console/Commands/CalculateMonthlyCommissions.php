<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\CommissionService;
use Carbon\Carbon;

class CalculateMonthlyCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:calculate {--month= : Month to calculate (YYYY-MM)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate monthly commissions for all active sellers';

    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        parent::__construct();
        $this->commissionService = $commissionService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $month = $this->option('month') ?? Carbon::now()->subMonth()->format('Y-m');

        $this->info("Calculating commissions for month: {$month}");

        // Get all sellers with active subscriptions
        $sellers = User::whereHas('subscriptions', function($query) use ($month) {
                $query->where('status', 'active')
                    ->where('starts_at', '<=', $month . '-01')
                    ->where('ends_at', '>=', $month . '-01');
            })
            ->get();

        if ($sellers->isEmpty()) {
            $this->warn('No active sellers found for this month.');
            return Command::FAILURE;
        }

        $this->info("Found {$sellers->count()} active sellers");

        $progressBar = $this->output->createProgressBar($sellers->count());
        $progressBar->start();

        $successful = 0;
        $failed = 0;
        $errors = [];

        foreach ($sellers as $seller) {
            try {
                $commission = $this->commissionService->calculateMonthlyCommission($seller, $month);
                
                $this->newLine();
                $this->info("✓ {$seller->name}: ₺{$commission->net_commission}");
                
                $successful++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("✗ {$seller->name}: {$e->getMessage()}");
                
                $failed++;
                $errors[] = [
                    'seller' => $seller->name,
                    'error' => $e->getMessage(),
                ];
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Commission Calculation Summary for {$month}");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Total Sellers: {$sellers->count()}");
        $this->info("Successful: {$successful}");
        $this->info("Failed: {$failed}");
        
        if (!empty($errors)) {
            $this->newLine();
            $this->error("Errors:");
            foreach ($errors as $error) {
                $this->error("  - {$error['seller']}: {$error['error']}");
            }
        }

        return $failed === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
