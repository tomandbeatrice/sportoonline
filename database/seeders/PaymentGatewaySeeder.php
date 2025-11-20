<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'iyzico',
                'provider' => 'App\Services\Payment\Gateways\IyzicoGateway',
                'display_name' => 'Iyzico',
                'description' => 'Türkiye\'nin önde gelen ödeme gateway\'i. Tüm banka kartları ve taksit seçenekleri.',
                'credentials' => [
                    'api_key' => '',
                    'secret_key' => '',
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'sort_order' => 1,
                'supported_currencies' => ['TRY', 'USD', 'EUR'],
                'min_amount' => 1.00,
                'max_amount' => 100000.00,
                'commission_rate' => 2.5,
                'fixed_commission' => 0.25,
            ],
            [
                'name' => 'paytr',
                'provider' => 'App\Services\Payment\Gateways\PayTRGateway',
                'display_name' => 'PayTR',
                'description' => 'Yerli ödeme çözümü. Düşük komisyon oranları ve hızlı entegrasyon.',
                'credentials' => [
                    'merchant_id' => '',
                    'merchant_key' => '',
                    'merchant_salt' => '',
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'sort_order' => 2,
                'supported_currencies' => ['TRY'],
                'min_amount' => 1.00,
                'max_amount' => 50000.00,
                'commission_rate' => 2.0,
                'fixed_commission' => 0.20,
            ],
            [
                'name' => 'mokapos',
                'provider' => 'App\Services\Payment\Gateways\MokaPosGateway',
                'display_name' => 'MokaPOS',
                'description' => 'Akbank sanal POS çözümü. Güvenli 3D Secure ödemeleri.',
                'credentials' => [
                    'dealer_code' => '',
                    'username' => '',
                    'password' => '',
                    'checkout_key' => '',
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'sort_order' => 3,
                'supported_currencies' => ['TRY'],
                'min_amount' => 1.00,
                'max_amount' => 75000.00,
                'commission_rate' => 2.3,
                'fixed_commission' => 0.30,
            ],
            [
                'name' => 'ziraatpay',
                'provider' => 'App\Services\Payment\Gateways\ZiraatPayGateway',
                'display_name' => 'ZiraatPay',
                'description' => 'Ziraat Bankası sanal POS. Kurumsal çözümler için uygundur.',
                'credentials' => [
                    'client_id' => '',
                    'store_key' => '',
                    'api_username' => '',
                    'api_password' => '',
                ],
                'is_active' => false,
                'is_test_mode' => true,
                'sort_order' => 4,
                'supported_currencies' => ['TRY'],
                'min_amount' => 1.00,
                'max_amount' => 100000.00,
                'commission_rate' => 2.2,
                'fixed_commission' => 0.25,
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['name' => $gateway['name']],
                $gateway
            );
        }

        $this->command->info('✅ Payment gateways seeded successfully!');
    }
}
