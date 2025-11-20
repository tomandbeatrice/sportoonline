<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeAndCountToInvoiceExportLogsTable extends Migration
{
    public function up()
    {
        Schema::table('invoice_export_logs', function (Blueprint $table) {
            $table->bigInteger('file_size')->nullable(); // 'after' kaldırıldı
            $table->integer('file_count')->nullable();   // 'after' kaldırıldı
        });
    }

    public function down()
    {
        Schema::table('invoice_export_logs', function (Blueprint $table) {
            $table->dropColumn(['file_size', 'file_count']);
        });
    }
}