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
        Schema::create('seller_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            
            // Payment Information
            $table->string('bank_name')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('iban', 32)->nullable();
            $table->string('branch_code')->nullable();
            
            // Shipping Settings
            $table->json('shipping_carriers')->nullable();
            $table->json('shipping_zones')->nullable();
            
            // Notification Preferences
            $table->json('email_notifications')->nullable();
            $table->json('sms_notifications')->nullable();
            $table->json('push_notifications')->nullable();
            
            // Tax Information
            $table->enum('company_type', ['individual', 'limited', 'joint_stock'])->nullable();
            $table->string('tax_office')->nullable();
            $table->string('tax_number', 11)->nullable();
            $table->string('trade_registry_number')->nullable();
            $table->string('company_title')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_settings');
    }
};
