<?php

namespace App\Console\Commands;

use App\Mail\CampaignExpiring;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCampaignExpiringNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:notify-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users about campaigns expiring in 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Checking for expiring campaigns...');

        // Find campaigns expiring in the next 24 hours
        $expiringCampaigns = Campaign::where('is_active', true)
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDay())
            ->get();

        if ($expiringCampaigns->isEmpty()) {
            $this->info('No campaigns expiring in the next 24 hours.');
            return;
        }

        $this->info("Found {$expiringCampaigns->count()} expiring campaigns.");

        // Get all active users who haven't been notified
        $users = User::whereNotNull('email_verified_at')->get();

        $sentCount = 0;

        foreach ($expiringCampaigns as $campaign) {
            $cacheKey = "campaign_expiring_notified_{$campaign->id}";
            
            // Skip if already notified
            if (\Cache::has($cacheKey)) {
                continue;
            }

            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new CampaignExpiring($campaign, $user));
                    $sentCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
                }
            }

            // Mark as notified (cache for 7 days)
            \Cache::put($cacheKey, true, now()->addDays(7));
            
            $this->info("Sent notifications for campaign: {$campaign->name}");
        }

        $this->info("Successfully sent {$sentCount} notification emails.");
    }
}
