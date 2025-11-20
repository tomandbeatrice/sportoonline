<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('title');
            $table->string('component'); // Vue component name
            $table->string('icon')->nullable();
            $table->string('type'); // metric, chart, list, card, table
            $table->string('size')->default('md'); // sm, md, lg, xl
            $table->string('user_type'); // admin, seller, buyer
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable(); // Module specific configuration
            $table->json('permissions')->nullable(); // Required permissions
            $table->timestamps();
            
            $table->index(['user_type', 'is_active', 'display_order']);
        });

        // Seed default modules
        DB::table('dashboard_modules')->insert([
            [
                'name' => 'orders_today',
                'title' => 'Bugünkü Siparişler',
                'component' => 'OrdersTodayCard',
                'icon' => 'shopping-cart',
                'type' => 'metric',
                'size' => 'md',
                'user_type' => 'admin',
                'display_order' => 1,
                'is_active' => true,
                'config' => json_encode(['refresh_interval' => 30]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'revenue_metrics',
                'title' => 'Gelir Metrikleri',
                'component' => 'RevenueMetricsCard',
                'icon' => 'trending-up',
                'type' => 'metric',
                'size' => 'lg',
                'user_type' => 'admin',
                'display_order' => 2,
                'is_active' => true,
                'config' => json_encode(['show_chart' => true, 'period' => 'month']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'active_campaigns',
                'title' => 'Aktif Kampanyalar',
                'component' => 'ActiveCampaignsCard',
                'icon' => 'gift',
                'type' => 'list',
                'size' => 'md',
                'user_type' => 'admin',
                'display_order' => 3,
                'is_active' => true,
                'config' => json_encode(['max_items' => 5]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'seller_performance',
                'title' => 'Satıcı Performansı',
                'component' => 'SellerPerformanceTable',
                'icon' => 'users',
                'type' => 'table',
                'size' => 'xl',
                'user_type' => 'admin',
                'display_order' => 4,
                'is_active' => true,
                'config' => json_encode(['limit' => 10, 'sort_by' => 'revenue']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'product_analytics',
                'title' => 'Ürün Analitiği',
                'component' => 'ProductAnalyticsChart',
                'icon' => 'package',
                'type' => 'chart',
                'size' => 'lg',
                'user_type' => 'admin',
                'display_order' => 5,
                'is_active' => true,
                'config' => json_encode(['chart_type' => 'bar']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_modules');
    }
};
