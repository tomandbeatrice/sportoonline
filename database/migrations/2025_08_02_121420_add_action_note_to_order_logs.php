<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('order_logs', 'action_type')) {
                $table->string('action_type')->default('status_change');
            }

            if (!Schema::hasColumn('order_logs', 'note')) {
                $table->text('note')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_logs', function (Blueprint $table) {
            if (Schema::hasColumn('order_logs', 'action_type')) {
                $table->dropColumn('action_type');
            }

            if (Schema::hasColumn('order_logs', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};