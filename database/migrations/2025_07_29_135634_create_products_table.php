<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable()->index(); // Rol bazlı filtreleme
            $table->unsignedBigInteger('category_id')->nullable()->index(); // Kategori ilişkisi
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->string('category')->nullable(); // Legacy support
            $table->string('imageUrl')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Geri alınabilir silme
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};