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
        // Kargo ücretleri tablosu (Satıcı bazlı)
        Schema::create('shipping_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Satıcı
            $table->string('region'); // İl veya bölge (İstanbul, Anadolu, vb)
            $table->decimal('fee', 8, 2); // Kargo ücreti
            $table->decimal('free_shipping_threshold', 10, 2)->default(0); // Ücretsiz kargo limiti
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['user_id', 'region']); // Her satıcı için bölge bazında tek kayıt
        });

        // Orders tablosuna kargo ve stopaj alanları ekle
        Schema::table('orders', function (Blueprint $table) {
            // Kargo bilgileri
            $table->decimal('shipping_fee', 8, 2)->default(0)->after('total_amount');
            $table->string('shipping_region')->nullable()->after('shipping_fee');
            $table->boolean('free_shipping_applied')->default(false)->after('shipping_region');
            
            // E-Ticaret Stopaj Kesintisi (Yeni yasa)
            $table->decimal('withholding_tax_rate', 5, 2)->default(1.00)->after('free_shipping_applied'); // %1 veya %2
            $table->decimal('withholding_tax_amount', 10, 2)->default(0)->after('withholding_tax_rate');
            
            // Satıcının net kazancı (komisyon + kargo + stopaj kesildikten sonra)
            $table->decimal('seller_net_amount', 10, 2)->default(0)->after('withholding_tax_amount');
            
            // Platform toplam geliri (komisyon + kargo + stopaj)
            $table->decimal('platform_revenue', 10, 2)->default(0)->after('seller_net_amount');
        });

        // Order items'a stopaj detayı ekle
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('withholding_tax_amount', 10, 2)->default(0)->after('subscription_commission_amount');
        });

        // Aylık komisyon tablosuna stopaj ve kargo ekle
        if (Schema::hasTable('monthly_commissions')) {
            Schema::table('monthly_commissions', function (Blueprint $table) {
                $table->decimal('total_shipping_fees', 10, 2)->default(0)->after('subscription_fee');
                $table->decimal('total_withholding_tax', 10, 2)->default(0)->after('total_shipping_fees');
                $table->decimal('adjusted_net_commission', 10, 2)->default(0)->after('total_withholding_tax');
                // adjusted_net = (commission - subscription_fee - shipping_fees - withholding_tax)
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_fees');
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_fee',
                'shipping_region',
                'free_shipping_applied',
                'withholding_tax_rate',
                'withholding_tax_amount',
                'seller_net_amount',
                'platform_revenue'
            ]);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('withholding_tax_amount');
        });

        if (Schema::hasTable('monthly_commissions')) {
            Schema::table('monthly_commissions', function (Blueprint $table) {
                $table->dropColumn([
                    'total_shipping_fees',
                    'total_withholding_tax',
                    'adjusted_net_commission'
                ]);
            });
        }
    }
};
