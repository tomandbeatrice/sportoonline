<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('avatar')->nullable();
            $table->string('license_number')->unique();
            $table->date('license_expiry');
            $table->string('license_type')->nullable();
            $table->string('id_number')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('experience_years')->default(0);
            $table->json('languages')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_trips')->default(0);
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('pending');
            $table->boolean('is_available')->default(true);
            $table->json('current_location')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_available']);
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
