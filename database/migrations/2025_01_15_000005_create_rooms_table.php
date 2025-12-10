<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('room_type');
            $table->text('description')->nullable();
            $table->integer('capacity')->default(2);
            $table->string('bed_type')->nullable();
            $table->integer('bed_count')->default(1);
            $table->integer('size')->nullable()->comment('in square meters');
            $table->decimal('price_per_night', 10, 2);
            $table->integer('quantity')->default(1);
            $table->json('amenities')->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['available', 'unavailable', 'maintenance'])->default('available');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
