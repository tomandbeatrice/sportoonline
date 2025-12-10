<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add gamification columns to users
        Schema::table('users', function (Blueprint $table) {
            $table->integer('points')->default(0);
            $table->integer('level')->default(1);
            $table->integer('current_streak')->default(0);
        });

        // Badges table
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable(); // emoji or url
            $table->string('description')->nullable();
            $table->integer('points_required')->default(0);
            $table->timestamps();
        });

        // User Badges pivot
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('badge_id')->constrained('badges')->onDelete('cascade');
            $table->timestamp('awarded_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['points', 'level', 'current_streak']);
        });
    }
};
