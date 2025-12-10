<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cuisine_type')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('district')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->json('working_hours')->nullable();
            $table->decimal('minimum_order', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->integer('delivery_time')->nullable()->comment('in minutes');
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->boolean('has_delivery')->default(true);
            $table->boolean('has_pickup')->default(true);
            $table->boolean('has_dine_in')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_featured']);
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
