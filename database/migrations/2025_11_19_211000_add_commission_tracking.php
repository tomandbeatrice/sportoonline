<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add commission tracking to order_items for subscription-based commission
        if (Schema::hasTable('order_items') && !Schema::hasColumn('order_items', 'subscription_commission_rate')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('subscription_commission_rate', 5, 2)->nullable();
                $table->decimal('subscription_commission_amount', 10, 2)->nullable();
            });
        }

        // Add monthly commission tracking
        Schema::create('monthly_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('month'); // YYYY-MM format
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('subscription_fee', 10, 2)->default(0);
            $table->decimal('net_commission', 10, 2)->default(0); // commission - subscription fee
            $table->integer('order_count')->default(0);
            $table->enum('status', ['pending', 'calculated', 'paid'])->default('pending');
            $table->date('paid_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_commissions');
        
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn(['subscription_commission_rate', 'subscription_commission_amount']);
            });
        }
    }
};
