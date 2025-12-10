<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('route_id')->nullable()->constrained()->nullOnDelete();
            $table->string('booking_number')->unique();
            $table->string('pickup_location');
            $table->string('pickup_address');
            $table->decimal('pickup_latitude', 10, 8)->nullable();
            $table->decimal('pickup_longitude', 11, 8)->nullable();
            $table->string('dropoff_location');
            $table->string('dropoff_address');
            $table->decimal('dropoff_latitude', 10, 8)->nullable();
            $table->decimal('dropoff_longitude', 11, 8)->nullable();
            $table->timestamp('pickup_datetime');
            $table->timestamp('estimated_arrival')->nullable();
            $table->timestamp('actual_pickup_time')->nullable();
            $table->timestamp('actual_dropoff_time')->nullable();
            $table->integer('passengers')->default(1);
            $table->integer('luggage_count')->default(0);
            $table->decimal('distance', 10, 2)->nullable();
            $table->integer('duration')->nullable()->comment('in minutes');
            $table->decimal('base_price', 10, 2);
            $table->decimal('extras_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'online'])->default('online');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('status', ['pending', 'confirmed', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('passenger_name');
            $table->string('passenger_phone');
            $table->string('passenger_email')->nullable();
            $table->string('flight_number')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['driver_id', 'status']);
            $table->index('pickup_datetime');
            $table->index('booking_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
