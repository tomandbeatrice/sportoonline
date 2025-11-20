<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalPdfAccessLogsTable extends Migration
{
    public function up(): void
    {
        Schema::create('external_pdf_access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->ipAddress('ip');
            $table->timestamp('accessed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_pdf_access_logs');
    }
}