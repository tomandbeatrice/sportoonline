<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CampaignAIOptimizer;
use Illuminate\Support\Facades\DB;

class TestCampaignAI extends Command
{
    protected $signature = 'campaign:test-ai';
    protected $description = 'Test Campaign AI Optimizer with sample data';

    protected CampaignAIOptimizer $optimizer;

    public function __construct(CampaignAIOptimizer $optimizer)
    {
        parent::__construct();
        $this->optimizer = $optimizer;
    }

    public function handle()
    {
        $this->info('🤖 Campaign AI Optimizer Test');
        $this->newLine();

        // Create sample campaign if not exists
        $campaign = DB::table('incentive_campaigns')->first();
        
        if (!$campaign) {
            $this->warn('⚠️  No campaigns found. Creating sample campaign...');
            
            $campaignId = DB::table('incentive_campaigns')->insertGetId([
                'name' => 'Test AI Campaign',
                'type' => 'discount',
                'discount_amount' => 20,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert sample performance data
            DB::table('campaign_logs')->insert([
                'campaign_id' => $campaignId,
                'order_id' => 1,
                'user_id' => 1,
                'discount_applied' => 20,
                'created_at' => now(),
            ]);

            $this->info("✅ Sample campaign created (ID: $campaignId)");
            $campaign = (object)['id' => $campaignId];
        }

        $this->info("Testing Campaign ID: {$campaign->id}");
        $this->newLine();

        // Test 1: Calculate Score
        $this->info('📊 Test 1: Calculating AI Score...');
        $score = $this->optimizer->calculateCampaignScore($campaign->id);
        $this->line("   Score: " . number_format($score, 2));
        $this->newLine();

        // Test 2: Generate Recommendations
        $this->info('💡 Test 2: Generating Recommendations...');
        $recommendations = $this->optimizer->generateRecommendations($campaign->id);
        foreach ($recommendations as $idx => $rec) {
            $this->line("   " . ($idx + 1) . ". [{$rec['type']}] {$rec['suggestion']}");
            $this->line("      Priority: {$rec['priority']} | Impact: {$rec['estimated_impact']}");
        }
        $this->newLine();

        // Test 3: Suggest Segments
        $this->info('👥 Test 3: Suggesting Customer Segments...');
        $segments = $this->optimizer->suggestSegments($campaign->id);
        foreach ($segments as $idx => $seg) {
            $this->line("   " . ($idx + 1) . ". {$seg['segment']} (~{$seg['estimated_size']} customers)");
            $this->line("      Reason: {$seg['reason']}");
        }
        $this->newLine();

        // Test 4: Predict Best Time
        $this->info('Test 4: Predicting Best Time...');
        $timing = $this->optimizer->predictBestTime([]);
        $this->line("   Best Hour: {$timing['best_hour']}:00");
        $this->line("   Best Day: {$timing['best_day_of_week']}");
        $this->line("   Reason: {$timing['reason']}");
        $this->newLine();

        $this->info('✅ All tests completed successfully!');
        
        return Command::SUCCESS;
    }
}
