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
        Schema::create('service_modules', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // marketplace, food, hotel...
            $table->string('name'); // Mağaza, Yemek...
            $table->string('icon')->nullable(); // 🛒, 🍔...
            $table->string('badge')->nullable(); // Yeni, 12...
            $table->string('badge_class')->nullable(); // bg-green-100 text-green-600
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->string('route')->nullable(); // /food, /hotel...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_modules');
    }
};
