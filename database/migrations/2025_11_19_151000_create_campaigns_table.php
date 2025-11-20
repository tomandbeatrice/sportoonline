<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed', 'buy_x_get_y', 'free_shipping'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // Percentage or fixed amount
            $table->integer('min_quantity')->default(1); // For buy_x_get_y
            $table->integer('free_quantity')->default(0); // For buy_x_get_y
            $table->decimal('min_purchase_amount', 10, 2)->default(0); // Minimum cart amount
            $table->string('coupon_code')->nullable()->unique(); // Optional coupon code
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('usage_per_customer')->default(1); // Per customer usage limit
            $table->integer('used_count')->default(0); // Current usage count
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->enum('applies_to', ['all_products', 'specific_products', 'specific_categories'])->default('all_products');
            $table->json('product_ids')->nullable(); // For specific products
            $table->json('category_ids')->nullable(); // For specific categories
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('campaign_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('order_id')->index();
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_usages');
        Schema::dropIfExists('campaigns');
    }
};
