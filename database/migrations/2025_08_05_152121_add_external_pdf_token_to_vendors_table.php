<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExternalPdfTokenToVendorsTable extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('vendors', 'external_pdf_token')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->string('external_pdf_token')->nullable();
            });

            // SQLite uyumu için index ayrı tanımlanır
            Schema::table('vendors', function (Blueprint $table) {
                $table->unique('external_pdf_token', 'vendors_external_pdf_token_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vendors', 'external_pdf_token')) {
            // Önce index kaldırılır
            Schema::table('vendors', function (Blueprint $table) {
                $table->dropUnique('vendors_external_pdf_token_unique');
            });

            // Sonra sütun kaldırılır
            Schema::table('vendors', function (Blueprint $table) {
                $table->dropColumn('external_pdf_token');
            });
        }
    }
}