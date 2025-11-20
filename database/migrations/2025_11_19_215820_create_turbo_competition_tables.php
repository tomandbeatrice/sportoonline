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
        // Monthly competition periods
        Schema::create('turbo_competitions', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'ended', 'finalized'])->default('active');
            $table->timestamps();
            
            $table->unique(['year', 'month']);
            $table->index('status');
        });

        // Competition winners and rewards
        Schema::create('turbo_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('turbo_competitions')->onDelete('cascade');
            $table->enum('category', ['customer', 'seller']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rank'); // 1, 2, 3
            $table->decimal('total_amount', 12, 2); // Spending or revenue
            $table->decimal('reward_money', 10, 2)->default(0);
            $table->integer('reward_points')->default(0);
            $table->string('coupon_code')->nullable(); // For sellers only
            $table->timestamps();
            
            $table->unique(['competition_id', 'category', 'user_id']);
            $table->index(['category', 'rank']);
        });

        // Seller commission discount coupons
        Schema::create('turbo_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('winner_id')->constrained('turbo_winners')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('code')->unique();
            $table->decimal('commission_discount_percent', 5, 2); // e.g., 5.00 for 5%
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->integer('max_uses')->default(1);
            $table->integer('used_count')->default(0);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->boolean('is_active')->default(true);
            $table->json('conditions')->nullable(); // Extra rules
            $table->timestamps();
            
            $table->index('seller_id');
            $table->index(['code', 'is_active']);
        });

        // Coupon usage history
        Schema::create('turbo_coupon_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('turbo_coupons')->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('original_commission', 10, 2);
            $table->decimal('discounted_commission', 10, 2);
            $table->decimal('savings', 10, 2);
            $table->timestamps();
        });

        // Add turbo points to users table if not exists
        if (!Schema::hasColumn('users', 'turbo_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('turbo_points')->default(0)->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turbo_coupon_usage');
        Schema::dropIfExists('turbo_coupons');
        Schema::dropIfExists('turbo_winners');
        Schema::dropIfExists('turbo_competitions');
        
        if (Schema::hasColumn('users', 'turbo_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('turbo_points');
            });
        }
    }
};
