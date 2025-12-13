<?php

namespace App\Services;

use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Refund as StripeRefund;

class PaymentGatewayService
{
    /**
     * Process refund based on payment gateway
     */
    public function refund(Payment $payment, float $amount, ?string $ipAddress = null): array
    {
        Log::info('Processing refund', [
            'payment_id' => $payment->id,
            'gateway' => $payment->payment_method,
            'amount' => $amount
        ]);

        return match($payment->payment_method) {
            'iyzico' => $this->refundIyzico($payment, $amount, $ipAddress),
            'paytr' => $this->refundPayTR($payment, $amount),
            'stripe' => $this->refundStripe($payment, $amount),
            default => throw new Exception('Desteklenmeyen ödeme yöntemi: ' . $payment->payment_method)
        };
    }

    /**
     * Refund via Iyzico
     */
    protected function refundIyzico(Payment $payment, float $amount, ?string $ipAddress = null): array
    {
        if (!$payment->transaction_id) {
            throw new Exception('İade için gerekli işlem ID bulunamadı.');
        }

        try {
            $options = new \Iyzipay\Options();
            $options->setApiKey(config('services.iyzico.api_key'));
            $options->setSecretKey(config('services.iyzico.secret_key'));
            $options->setBaseUrl(config('services.iyzico.base_url'));

            $request = new \Iyzipay\Request\CreateRefundRequest();
            $request->setLocale(\Iyzipay\Model\Locale::TR);
            $request->setConversationId($payment->id);
            $request->setPaymentTransactionId($payment->transaction_id);
            $request->setPrice($amount);
            $request->setIp($ipAddress ?? '127.0.0.1');
            $request->setCurrency(\Iyzipay\Model\Currency::TL);

            $refund = \Iyzipay\Model\Refund::create($request, $options);

            if ($refund->getStatus() === 'success') {
                return [
                    'success' => true,
                    'refund_id' => $refund->getPaymentId(),
                    'amount' => $amount,
                    'gateway' => 'iyzico',
                    'message' => 'İade başarıyla işleme alındı.'
                ];
            }

            throw new Exception('Iyzico iade hatası: ' . $refund->getErrorMessage());
        } catch (Exception $e) {
            Log::error('Iyzico refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            throw new Exception('İade işlemi başarısız: ' . $e->getMessage());
        }
    }

    /**
     * Refund via PayTR
     */
    protected function refundPayTR(Payment $payment, float $amount): array
    {
        if (!$payment->transaction_id) {
            throw new Exception('İade için gerekli işlem ID bulunamadı.');
        }

        try {
            $merchantId = config('services.paytr.merchant_id');
            $merchantKey = config('services.paytr.merchant_key');
            $merchantSalt = config('services.paytr.merchant_salt');

            $amountInKurus = (int)($amount * 100); // Convert to kurus

            $hashString = $merchantId . $payment->transaction_id . $amountInKurus . $merchantSalt;
            $token = base64_encode(hash_hmac('sha256', $hashString, $merchantKey, true));

            $data = [
                'merchant_id' => $merchantId,
                'order_id' => $payment->transaction_id,
                'return_amount' => $amountInKurus,
                'paytr_token' => $token,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/iade');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, config('app.env') === 'production');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, config('app.env') === 'production' ? 2 : 0);

            $result = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($result, true);

            if ($response && $response['status'] === 'success') {
                return [
                    'success' => true,
                    'refund_id' => $response['return_id'] ?? $payment->transaction_id,
                    'amount' => $amount,
                    'gateway' => 'paytr',
                    'message' => 'İade başarıyla işleme alındı.'
                ];
            }

            throw new Exception('PayTR iade hatası: ' . ($response['reason'] ?? 'Bilinmeyen hata'));
        } catch (Exception $e) {
            Log::error('PayTR refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            throw new Exception('İade işlemi başarısız: ' . $e->getMessage());
        }
    }

    /**
     * Refund via Stripe
     */
    protected function refundStripe(Payment $payment, float $amount): array
    {
        if (!$payment->transaction_id) {
            throw new Exception('İade için gerekli işlem ID bulunamadı.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $refund = StripeRefund::create([
                'charge' => $payment->transaction_id,
                'amount' => (int)($amount * 100), // Convert to cents
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $amount,
                'gateway' => 'stripe',
                'message' => 'İade başarıyla işleme alındı.'
            ];
        } catch (Exception $e) {
            Log::error('Stripe refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            throw new Exception('İade işlemi başarısız: ' . $e->getMessage());
        }
    }

    /**
     * Check if refund is supported for this payment method
     */
    public function supportsRefund(string $paymentMethod): bool
    {
        return in_array($paymentMethod, ['iyzico', 'paytr', 'stripe']);
    }

    /**
     * Get refund status from gateway
     */
    public function getRefundStatus(Payment $payment, string $refundId): array
    {
        return match($payment->payment_method) {
            'iyzico' => $this->getIyzicoRefundStatus($payment, $refundId),
            'paytr' => $this->getPayTRRefundStatus($payment, $refundId),
            'stripe' => $this->getStripeRefundStatus($payment, $refundId),
            default => ['status' => 'unknown', 'message' => 'Desteklenmeyen ödeme yöntemi']
        };
    }

    protected function getIyzicoRefundStatus(Payment $payment, string $refundId): array
    {
        // Implementation for checking Iyzico refund status
        return ['status' => 'pending', 'message' => 'İade durumu kontrol ediliyor'];
    }

    protected function getPayTRRefundStatus(Payment $payment, string $refundId): array
    {
        // Implementation for checking PayTR refund status
        return ['status' => 'pending', 'message' => 'İade durumu kontrol ediliyor'];
    }

    protected function getStripeRefundStatus(Payment $payment, string $refundId): array
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $refund = StripeRefund::retrieve($refundId);
            
            return [
                'status' => $refund->status,
                'message' => 'İade durumu: ' . $refund->status
            ];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
