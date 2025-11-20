<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaign_performance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->decimal('ai_score', 5, 2); // AI calculated score (0-100)
            $table->string('grade', 5); // A+, A, B, C, D, F
            $table->decimal('conversion_rate', 5, 2);
            $table->decimal('revenue_impact', 12, 2);
            $table->decimal('engagement_rate', 5, 2);
            $table->decimal('roi', 8, 2);
            $table->integer('total_participants')->default(0);
            $table->integer('total_conversions')->default(0);
            $table->decimal('avg_order_value', 10, 2)->default(0);
            $table->json('hourly_performance')->nullable(); // Hourly breakdown for timing analysis
            $table->json('segment_performance')->nullable(); // Performance by customer segment
            $table->timestamp('snapshot_at');
            $table->timestamps();

            // Index without foreign key constraint
            $table->index(['campaign_id', 'snapshot_at']);
            $table->index('ai_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_performance_snapshots');
    }
};
