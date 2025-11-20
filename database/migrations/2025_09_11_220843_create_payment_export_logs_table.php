<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_export_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->json('filters')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamp('exported_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_export_logs');
    }
};