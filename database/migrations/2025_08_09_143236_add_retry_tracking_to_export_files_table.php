<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('export_files', function (Blueprint $table) {
            $table->timestamp('retry_started_at')->nullable();
            $table->timestamp('retry_finished_at')->nullable();
            $table->string('retry_status')->nullable();
            $table->text('retry_log')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('export_files', function (Blueprint $table) {
            $table->dropColumn([
                'retry_started_at',
                'retry_finished_at',
                'retry_status',
                'retry_log',
            ]);
        });
    }
};