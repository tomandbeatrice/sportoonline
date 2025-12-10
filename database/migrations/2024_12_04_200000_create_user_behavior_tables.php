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
        // Ürün görüntüleme kayıtları
        if (!Schema::hasTable('product_views')) {
            Schema::create('product_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('session_id')->nullable()->index();
                $table->string('source')->nullable(); // search, category, recommendation, direct
                $table->string('referrer')->nullable();
                $table->integer('duration_seconds')->default(0); // Sayfada kalma süresi
                $table->string('device_type')->nullable(); // mobile, desktop, tablet
                $table->string('ip_address')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'product_id']);
                $table->index(['product_id', 'created_at']);
            });
        }

        // Kampanya görüntüleme kayıtları
        if (!Schema::hasTable('campaign_views')) {
            Schema::create('campaign_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
                $table->string('session_id')->nullable()->index();
                $table->string('placement')->nullable(); // homepage_banner, sidebar, popup
                $table->string('device_type')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();

                $table->index(['campaign_id', 'created_at']);
            });
        }

        // Kampanya tıklama kayıtları
        if (!Schema::hasTable('campaign_clicks')) {
            Schema::create('campaign_clicks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
                $table->string('session_id')->nullable()->index();
                $table->string('click_target')->nullable(); // cta_button, banner, product_link
                $table->string('device_type')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();

                $table->index(['campaign_id', 'created_at']);
            });
        }

        // Arama geçmişi
        if (!Schema::hasTable('search_history')) {
            Schema::create('search_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('session_id')->nullable()->index();
                $table->string('query');
                $table->integer('results_count')->default(0);
                $table->boolean('clicked_result')->default(false);
                $table->foreignId('clicked_product_id')->nullable()->constrained('products')->onDelete('set null');
                $table->string('device_type')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index('query');
            });
        }

        // Sepete ekleme davranışları (terk edilen sepetler için)
        if (!Schema::hasTable('cart_events')) {
            Schema::create('cart_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('session_id')->nullable()->index();
                $table->enum('event_type', ['add', 'remove', 'update_quantity']);
                $table->integer('quantity')->default(1);
                $table->decimal('price_at_event', 10, 2);
                $table->string('source')->nullable(); // product_page, recommendation, wishlist
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_events');
        Schema::dropIfExists('search_history');
        Schema::dropIfExists('campaign_clicks');
        Schema::dropIfExists('campaign_views');
        Schema::dropIfExists('product_views');
    }
};
