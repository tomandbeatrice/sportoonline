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
        // İade talepleri tablosu
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('return_number')->unique();
            
            // İade sebebi
            $table->string('reason_category'); // defective, wrong_item, not_as_described, damaged, size_fit, changed_mind, late_delivery, other
            $table->string('reason')->nullable(); // Kısa sebep açıklaması
            $table->text('description')->nullable(); // Detaylı açıklama
            
            // Durum
            $table->string('status')->default('pending'); // pending, approved, rejected, shipped, received, inspecting, refunded, completed, cancelled
            
            // Miktar ve tutar
            $table->integer('quantity')->default(1);
            $table->decimal('refund_amount', 10, 2)->nullable();
            
            // Para iadesi bilgileri
            $table->string('refund_method')->nullable(); // original, wallet, bank_transfer
            $table->string('refund_status')->default('pending'); // pending, processing, completed, failed
            $table->timestamp('refunded_at')->nullable();
            
            // Görseller
            $table->json('images')->nullable();
            
            // Notlar
            $table->text('admin_notes')->nullable();
            $table->text('vendor_notes')->nullable();
            $table->text('customer_notes')->nullable();
            
            // Kargo bilgileri
            $table->string('shipping_label_url')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('shipping_carrier')->nullable();
            
            // Süreç tarihleri
            $table->timestamp('received_at')->nullable();
            $table->timestamp('inspected_at')->nullable();
            $table->text('inspection_notes')->nullable();
            
            // Onay/Red bilgileri
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // İndeksler
            $table->index('status');
            $table->index('refund_status');
            $table->index('reason_category');
            $table->index('created_at');
        });

        // İade log tablosu
        Schema::create('return_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('action'); // created, approved, rejected, shipped, received, inspected, refund_initiated, refund_completed, refund_failed, completed, cancelled, note_added
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_logs');
        Schema::dropIfExists('return_requests');
    }
};
