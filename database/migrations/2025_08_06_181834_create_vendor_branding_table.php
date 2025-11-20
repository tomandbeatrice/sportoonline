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
        Schema::create('vendor_branding', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_branding');
    }
};
Schema::create('vendor_branding', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
    $table->string('logo_url')->nullable();
    $table->string('primary_color')->nullable();
    $table->string('secondary_color')->nullable();
    $table->string('font_family')->nullable();
    $table->timestamps();
});
