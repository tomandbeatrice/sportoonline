<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->string('plate_number')->unique();
            $table->string('color')->nullable();
            $table->enum('vehicle_type', ['sedan', 'suv', 'van', 'minibus', 'bus', 'luxury'])->default('sedan');
            $table->integer('capacity')->default(4);
            $table->string('fuel_type')->nullable();
            $table->string('transmission')->nullable();
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('inspection_expiry')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_available']);
            $table->index('vehicle_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
