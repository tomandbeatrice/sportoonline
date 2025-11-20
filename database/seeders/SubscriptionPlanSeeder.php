<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // 🆓 SADECE KOMİSYON MODELİ (Abonelik Yok)
            [
                'name' => 'Sadece Komisyon',
                'slug' => 'commission-only',
                'description' => 'Abonelik ücreti yok, sadece satış başına %20 komisyon. Sınırsız ürün, ödeme yapmadan başlayın!',
                'price' => 0.00,
                'yearly_price' => 0.00,
                'product_limit' => 999999, // Sınırsız
                'image_limit_per_product' => 10,
                'max_file_size_mb' => 5,
                'bulk_upload' => false,
                'advanced_analytics' => false,
                'priority_support' => false,
                'api_access' => false,
                'commission_rate' => 20.00, // %20 komisyon, aylık ücret YOK
                'is_active' => true,
                'trial_days' => 0,
                'features' => [
                    '✅ Sınırsız ürün',
                    '✅ Ürün başına 10 fotoğraf',
                    '✅ Temel raporlama',
                    '✅ Email destek',
                    '💰 ₺0 aylık ücret',
                    '💰 %20 satış komisyonu',
                    '⚡ Anında başlayın',
                ],
            ],
            
            // 📦 ABONELİK SİSTEMİ (Ürün Limitine Göre)
            [
                'name' => 'Küçük Paket',
                'slug' => 'small',
                'description' => '50 ürüne kadar satış yapın, düşük komisyon avantajı.',
                'price' => 49.00, // ₺49/ay + %12 komisyon
                'yearly_price' => 490.00, // 2 ay ücretsiz
                'product_limit' => 50,
                'image_limit_per_product' => 10,
                'max_file_size_mb' => 5,
                'bulk_upload' => true,
                'advanced_analytics' => false,
                'priority_support' => false,
                'api_access' => false,
                'commission_rate' => 12.00,
                'is_active' => true,
                'trial_days' => 14,
                'features' => [
                    '📦 50 ürün limiti',
                    '✅ Ürün başına 10 fotoğraf',
                    '✅ CSV toplu yükleme',
                    '✅ Temel analitik',
                    '💰 ₺49/ay',
                    '💰 %12 satış komisyonu',
                    '🎁 14 gün ücretsiz deneme',
                ],
            ],
            [
                'name' => 'Orta Paket',
                'slug' => 'medium',
                'description' => '200 ürüne kadar, gelişmiş özellikler ve daha düşük komisyon.',
                'price' => 149.00, // ₺149/ay + %10 komisyon
                'yearly_price' => 1490.00,
                'product_limit' => 200,
                'image_limit_per_product' => 15,
                'max_file_size_mb' => 10,
                'bulk_upload' => true,
                'advanced_analytics' => true,
                'priority_support' => false,
                'api_access' => false,
                'commission_rate' => 10.00,
                'is_active' => true,
                'trial_days' => 14,
                'features' => [
                    '📦 200 ürün limiti',
                    '✅ Ürün başına 15 fotoğraf',
                    '✅ CSV ve Excel toplu yükleme',
                    '✅ Gelişmiş analitik dashboard',
                    '💰 ₺149/ay',
                    '💰 %10 satış komisyonu',
                    '🎁 14 gün ücretsiz deneme',
                ],
            ],
            [
                'name' => 'Büyük Paket',
                'slug' => 'large',
                'description' => '1000 ürüne kadar, profesyonel satıcılar için.',
                'price' => 399.00, // ₺399/ay + %8 komisyon
                'yearly_price' => 3990.00,
                'product_limit' => 1000,
                'image_limit_per_product' => 20,
                'max_file_size_mb' => 15,
                'bulk_upload' => true,
                'advanced_analytics' => true,
                'priority_support' => true,
                'api_access' => true,
                'commission_rate' => 8.00,
                'is_active' => true,
                'trial_days' => 7,
                'features' => [
                    '📦 1,000 ürün limiti',
                    '✅ Ürün başına 20 fotoğraf',
                    '✅ CSV ve Excel toplu yükleme',
                    '✅ Tam analitik dashboard',
                    '✅ API erişimi',
                    '💰 ₺399/ay',
                    '💰 %8 satış komisyonu',
                    '🎁 7 gün ücretsiz deneme',
                    '📞 Öncelikli destek',
                ],
            ],
            [
                'name' => 'Kurumsal Paket',
                'slug' => 'corporate',
                'description' => 'Sınırsız ürün, en düşük komisyon, tüm özellikler.',
                'price' => 999.00, // ₺999/ay + %5 komisyon (EN DÜŞÜK!)
                'yearly_price' => 9990.00,
                'product_limit' => 999999,
                'image_limit_per_product' => 20,
                'max_file_size_mb' => 20,
                'bulk_upload' => true,
                'advanced_analytics' => true,
                'priority_support' => true,
                'api_access' => true,
                'commission_rate' => 5.00, // %5 komisyon (EN DÜŞÜK!)
                'is_active' => true,
                'trial_days' => 7,
                'features' => [
                    '📦 Sınırsız ürün',
                    '✅ Ürün başına 20 fotoğraf',
                    '✅ CSV ve Excel toplu yükleme',
                    '✅ Tam analitik ve raporlama',
                    '✅ API erişimi',
                    '✅ Özel entegrasyonlar',
                    '💰 ₺999/ay',
                    '💰 %5 satış komisyonu (EN DÜŞÜK!)',
                    '🎁 7 gün ücretsiz deneme',
                    '📞 7/24 VIP destek',
                    '👤 Özel hesap yöneticisi',
                    '🎨 White-label seçenekleri',
                ],
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }

        $this->command->info('✅ 5 abonelik planı oluşturuldu (1 sadece komisyon + 4 abonelik paketi)!');
    }
}
