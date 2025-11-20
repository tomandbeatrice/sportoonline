<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // Who made the change
            $table->enum('type', ['add', 'remove', 'set']); // add: increase, remove: decrease, set: direct set
            $table->integer('quantity'); // Amount changed
            $table->integer('old_stock'); // Stock before change
            $table->integer('new_stock'); // Stock after change
            $table->text('note')->nullable(); // Reason for change
            $table->string('source')->nullable(); // order, manual, import, etc.
            $table->unsignedBigInteger('order_id')->nullable()->index(); // If related to an order
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });

        // Add low_stock_threshold to products table if not exists
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->integer('low_stock_threshold')->default(10)->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
        
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->dropColumn('low_stock_threshold');
            }
        });
    }
};
