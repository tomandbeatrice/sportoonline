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
        Schema::create('campaign_ai_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->string('recommendation_type'); // conversion_optimization, engagement_enhancement, etc.
            $table->string('action'); // Action taken
            $table->text('suggestion')->nullable(); // AI suggestion text
            $table->decimal('ai_score_before', 5, 2)->nullable(); // Score before action
            $table->decimal('ai_score_after', 5, 2)->nullable(); // Score after action
            $table->json('metrics_before')->nullable(); // Metrics snapshot before
            $table->json('metrics_after')->nullable(); // Metrics snapshot after
            $table->string('status')->default('pending'); // pending, applied, completed, failed
            $table->text('result_notes')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Index without foreign key constraint
            $table->index(['campaign_id', 'status']);
            $table->index('applied_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_ai_actions');
    }
};
