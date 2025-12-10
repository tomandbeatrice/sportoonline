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
        Schema::table('return_requests', function (Blueprint $table) {
            $table->string('return_shipping_code')->nullable()->after('shipping_label_url')
                ->comment('Satıcının müşteriye verdiği ücretsiz iade kargo kodu');
            $table->string('return_shipping_carrier')->nullable()->after('return_shipping_code')
                ->comment('İade kargo firması');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn(['return_shipping_code', 'return_shipping_carrier']);
        });
    }
};
