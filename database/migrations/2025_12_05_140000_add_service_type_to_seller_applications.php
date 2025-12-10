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
        Schema::table('seller_applications', function (Blueprint $table) {
            // Hizmet türü: products, food, hotel, transport, services, career
            $table->string('service_type')->default('products')->after('status');
            
            // Hizmet türüne özel veriler (JSON)
            // Örnek: cuisines, room_count, vehicle_count, skills vb.
            $table->json('service_data')->nullable()->after('service_type');
            
            // İndeks ekle - hızlı filtreleme için
            $table->index('service_type');
            $table->index(['status', 'service_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_applications', function (Blueprint $table) {
            $table->dropIndex(['service_type']);
            $table->dropIndex(['status', 'service_type']);
            $table->dropColumn(['service_type', 'service_data']);
        });
    }
};
