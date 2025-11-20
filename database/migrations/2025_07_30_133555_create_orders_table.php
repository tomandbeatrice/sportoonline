<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->decimal('total_price', 10, 2)->default(0);

            $table->string('name');
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            $table->string('payment_method')->default('cash'); // cash, stripe, iyzico
            $table->json('cart')->nullable(); // JSON sepet snapshot

            $table->string('status')->default('pending'); // pending, preparing, shipped, completed

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}