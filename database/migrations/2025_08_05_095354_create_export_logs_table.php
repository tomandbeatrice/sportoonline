<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportLogsTable extends Migration
{
    public function up(): void
    {
      Schema::create('export_logs', function (Blueprint $table) {
    $table->id();
    $table->string('segment');               // ✅ customer/vendor ayrımı
    $table->string('export_type');           // ✅ CSV, PDF, XLS gibi türler
    $table->string('status')->default('completed'); // ✅ completed, failed, pending
    $table->decimal('duration', 5, 2);       // ✅ işlem süresi (sn)
    $table->string('file_name')->nullable(); // ✅ dosya adı
    $table->decimal('file_size', 8, 2)->nullable(); // ✅ KB/MB cinsinden
    $table->json('filters')->nullable();     // ✅ uygulanan filtreler
    $table->timestamp('exported_at')->nullable(); // ✅ export zamanı
    $table->timestamps();                    // ✅ created_at / updated_at
});
    }

    public function down(): void
    {
        Schema::dropIfExists('export_logs');
    }
}