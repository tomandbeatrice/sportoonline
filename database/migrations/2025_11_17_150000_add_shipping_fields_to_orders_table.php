<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_provider')->nullable()->after('status');
            $table->string('tracking_code')->nullable()->after('shipping_provider');
            $table->string('shipping_status')->nullable()->after('tracking_code');
            $table->string('shipping_name')->nullable()->after('name');
            $table->text('shipping_address')->nullable()->after('shipping_name');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_district')->nullable()->after('shipping_city');
            $table->string('phone')->nullable()->after('email');
            $table->decimal('total_amount', 10, 2)->nullable()->after('total_price');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_provider',
                'tracking_code',
                'shipping_status',
                'shipping_name',
                'shipping_address',
                'shipping_city',
                'shipping_district',
                'phone',
                'total_amount',
            ]);
        });
    }
};
