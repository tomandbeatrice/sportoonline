<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('origin');
            $table->string('origin_code')->nullable();
            $table->decimal('origin_latitude', 10, 8)->nullable();
            $table->decimal('origin_longitude', 11, 8)->nullable();
            $table->string('destination');
            $table->string('destination_code')->nullable();
            $table->decimal('destination_latitude', 10, 8)->nullable();
            $table->decimal('destination_longitude', 11, 8)->nullable();
            $table->decimal('distance', 10, 2)->comment('in kilometers');
            $table->integer('estimated_duration')->comment('in minutes');
            $table->decimal('base_price', 10, 2);
            $table->decimal('price_per_km', 10, 2)->default(0);
            $table->enum('route_type', ['airport', 'intercity', 'city', 'custom'])->default('city');
            $table->json('waypoints')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['route_type', 'is_active']);
            $table->index('is_popular');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
