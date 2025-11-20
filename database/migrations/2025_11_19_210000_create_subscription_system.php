<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Subscription plans table
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Basic, Premium, Enterprise, Unlimited
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Monthly price
            $table->decimal('yearly_price', 10, 2)->nullable(); // Yearly discount price
            $table->integer('product_limit')->default(100);
            $table->integer('image_limit_per_product')->default(10);
            $table->integer('max_file_size_mb')->default(5); // MB per file
            $table->boolean('bulk_upload')->default(false);
            $table->boolean('advanced_analytics')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('api_access')->default(false);
            $table->decimal('commission_rate', 5, 2)->default(15.00); // % commission
            $table->boolean('is_active')->default(true);
            $table->integer('trial_days')->default(0);
            $table->json('features')->nullable(); // Additional features
            $table->timestamps();
        });

        // User subscriptions table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'cancelled', 'expired', 'trial'])->default('trial');
            $table->date('trial_ends_at')->nullable();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->date('cancelled_at')->nullable();
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('payment_gateway')->nullable(); // iyzico, stripe, etc.
            $table->string('gateway_subscription_id')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->decimal('commission_rate', 5, 2)->default(15.00); // Actual commission for this subscription
            $table->decimal('monthly_commission_cap', 10, 2)->nullable(); // Max commission per month
            $table->json('commission_settings')->nullable(); // Additional commission rules
            $table->timestamps();
        });

        // Subscription payments history
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method');
            $table->string('transaction_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('paid_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('subscription_month')->nullable(); // YYYY-MM for tracking
            $table->decimal('commission_rate', 5, 2)->nullable(); // Commission rate at payment time
            $table->timestamps();
        });

        // Add subscription fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')->nullable()->after('role')->constrained('subscription_plans')->onDelete('set null');
            $table->enum('subscription_status', ['active', 'inactive', 'trial', 'expired'])->default('trial')->after('subscription_plan_id');
            $table->date('subscription_ends_at')->nullable()->after('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn(['subscription_plan_id', 'subscription_status', 'subscription_ends_at']);
        });
        
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
