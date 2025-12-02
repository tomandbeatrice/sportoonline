<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaytrService implements PaymentGatewayInterface
{
    protected $merchantId;
    protected $merchantKey;
    protected $merchantSalt;
    protected $testMode;
    protected $apiUrl;
    protected $currency;

    public function __construct()
    {
        $this->merchantId = config('paytr.merchant_id');
        $this->merchantKey = config('paytr.merchant_key');
        $this->merchantSalt = config('paytr.merchant_salt');
        $this->testMode = config('paytr.test_mode') ? 1 : 0;
        $this->apiUrl = config('paytr.api_url');
        $this->currency = config('paytr.currency');
    }

    /**
     * Ödeme işlemi başlatır ve PayTR token'ını alır
     */
    public function initializePayment(Order $order)
    {
        $user = $order->user;
        $email = $user->email;
        $payment_amount = intval($order->total * 100); // Kuruş cinsinden
        $merchant_oid = "ORDER_" . $order->id . "_" . time();
        $user_name = $user->name;
        $user_address = $user->address ?? "Belirtilmemiş";
        $user_phone = $user->phone ?? "0000000000";

        // Sipariş içindeki ürünleri basket formatına çevir
        $user_basket = $this->prepareBasket($order);

        // Callback URL'leri
        $merchant_ok_url = route('payment.paytr.success');
        $merchant_fail_url = route('payment.paytr.failure');

        // Hash oluştur
        $paytr_token = $this->generateHash(
            $merchant_oid,
            $email,
            $payment_amount,
            $user_basket,
            $this->testMode
        );

        // PayTR API'ye gönderilecek data
        $post_vals = [
            'merchant_id' => $this->merchantId,
            'user_ip' => request()->ip(),
            'merchant_oid' => $merchant_oid,
            'email' => $email,
            'payment_amount' => $payment_amount,
            'paytr_token' => $paytr_token,
            'user_basket' => base64_encode($user_basket),
            'debug_on' => $this->testMode,
            'no_installment' => 0,
            'max_installment' => 0,
            'user_name' => $user_name,
            'user_address' => $user_address,
            'user_phone' => $user_phone,
            'merchant_ok_url' => $merchant_ok_url,
            'merchant_fail_url' => $merchant_fail_url,
            'timeout_limit' => 30,
            'currency' => $this->currency,
            'lang' => config('paytr.language'),
        ];

        // API isteği gönder
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('PayTR cURL Error: ' . curl_error($ch));
            curl_close($ch);
            throw new \Exception('PayTR bağlantı hatası');
        }

        curl_close($ch);

        $result = json_decode($result, true);

        if ($result['status'] === 'success') {
            // Transaction ID'yi sipariş kaydına ekle
            $order->update(['transaction_id' => $merchant_oid]);

            return [
                'token' => $result['token'],
                'iframe_url' => config('paytr.iframe_url') . $result['token']
            ];
        } else {
            Log::error('PayTR Token Error: ' . $result['reason']);
            throw new \Exception($result['reason'] ?? 'PayTR token alınamadı');
        }
    }

    /**
     * Sipariş ürünlerini PayTR basket formatına çevirir
     */
    protected function prepareBasket(Order $order)
    {
        $basket_items = [];

        foreach ($order->items as $item) {
            $basket_items[] = [
                $item->product->name,
                $item->price,
                $item->quantity
            ];
        }

        return json_encode($basket_items);
    }

    /**
     * PayTR için hash oluşturur
     */
    protected function generateHash($merchant_oid, $email, $payment_amount, $user_basket, $no_installment)
    {
        $hash_str = $this->merchantId . 
                    request()->ip() . 
                    $merchant_oid . 
                    $email . 
                    $payment_amount . 
                    $user_basket . 
                    $no_installment . 
                    0 . // max_installment
                    $this->currency . 
                    $this->testMode;

        return base64_encode(hash_hmac('sha256', $hash_str . $this->merchantSalt, $this->merchantKey, true));
    }

    /**
     * PayTR callback'ten gelen hash'i doğrular
     */
    public function verifyCallback($post)
    {
        $hash = base64_encode(hash_hmac('sha256', $post['merchant_oid'] . $this->merchantSalt . $post['status'] . $post['total_amount'], $this->merchantKey, true));

        if ($hash !== $post['hash']) {
            Log::error('PayTR Hash mismatch', ['expected' => $hash, 'received' => $post['hash']]);
            return false;
        }

        return true;
    }

    /**
     * Callback'ten gelen merchant_oid'den sipariş ID'sini çıkarır
     */
    public function extractOrderId($merchant_oid)
    {
        // FORMAT: ORDER_{id}_{timestamp}
        $parts = explode('_', $merchant_oid);
        return isset($parts[1]) ? intval($parts[1]) : null;
    }
}
