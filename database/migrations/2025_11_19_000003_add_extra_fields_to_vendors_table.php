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
        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'logo_url')) {
                $table->string('logo_url')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'cover_url')) {
                $table->string('cover_url')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'postal_code')) {
                $table->string('postal_code', 10)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['logo_url', 'cover_url', 'city', 'postal_code']);
        });
    }
};
