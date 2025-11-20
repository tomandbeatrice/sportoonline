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
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('title')->after('rating')->nullable();
            $table->text('pros')->after('comment')->nullable();
            $table->text('cons')->after('pros')->nullable();
            $table->json('photos')->after('cons')->nullable();
            $table->boolean('verified_purchase')->after('photos')->default(false);
            $table->integer('helpful_count')->after('verified_purchase')->default(0);
            $table->integer('not_helpful_count')->after('helpful_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['title', 'pros', 'cons', 'photos', 'verified_purchase', 'helpful_count', 'not_helpful_count']);
        });
    }
};
