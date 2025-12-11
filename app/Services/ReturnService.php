<?php

namespace App\Services;

use App\Models\ReturnRequest;
use App\Models\ReturnLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ReturnService
{
    /**
     * Yeni iade talebi oluştur
     */
    public function createReturnRequest(array $data): ReturnRequest
    {
        return DB::transaction(function () use ($data) {
            // Sipariş kontrolü
            $order = Order::findOrFail($data['order_id']);
            
            // Sipariş sahibi kontrolü
            if ($order->user_id !== auth()->id()) {
                throw new Exception('Bu sipariş size ait değil.');
            }
            
            // Sipariş durumu kontrolü (teslim edilmiş olmalı)
            if (!in_array($order->status, ['delivered', 'completed'])) {
                throw new Exception('Sadece teslim edilmiş siparişler için iade talebi oluşturabilirsiniz.');
            }
            
            // İade süresi kontrolü (14 gün)
            $deliveredAt = $order->delivered_at ?? $order->updated_at;
            if ($deliveredAt->diffInDays(now()) > 14) {
                throw new Exception('İade süresi (14 gün) dolmuştur.');
            }
            
            // Sipariş kalemi varsa kontrol et
            $orderItem = null;
            if (!empty($data['order_item_id'])) {
                $orderItem = OrderItem::where('id', $data['order_item_id'])
                    ->where('order_id', $order->id)
                    ->first();
                    
                if (!$orderItem) {
                    throw new Exception('Sipariş kalemi bulunamadı.');
                }
                
                // Bu kalem için zaten iade talebi var mı?
                $existingReturn = ReturnRequest::where('order_item_id', $orderItem->id)
                    ->whereNotIn('status', [ReturnRequest::STATUS_CANCELLED, ReturnRequest::STATUS_REJECTED])
                    ->first();
                    
                if ($existingReturn) {
                    throw new Exception('Bu ürün için zaten bir iade talebi mevcut.');
                }
            }
            
            // İade tutarını hesapla
            $refundAmount = $this->calculateRefundAmount($order, $orderItem, $data['quantity'] ?? 1);
            
            // İade talebi oluştur
            $returnRequest = ReturnRequest::create([
                'order_id' => $order->id,
                'order_item_id' => $orderItem?->id,
                'user_id' => auth()->id(),
                'vendor_id' => $orderItem?->vendor_id ?? $order->vendor_id,
                'reason_category' => $data['reason_category'],
                'reason' => $data['reason'] ?? null,
                'description' => $data['description'] ?? null,
                'quantity' => $data['quantity'] ?? 1,
                'refund_amount' => $refundAmount,
                'images' => $data['images'] ?? null,
                'customer_notes' => $data['customer_notes'] ?? null,
            ]);
            
            // Log kaydı
            $this->logAction($returnRequest, ReturnLog::ACTION_CREATED, null, ReturnRequest::STATUS_PENDING, 'İade talebi oluşturuldu.');
            
            return $returnRequest;
        });
    }
    
    /**
     * İade tutarını hesapla
     */
    public function calculateRefundAmount(Order $order, ?OrderItem $orderItem, int $quantity = 1): float
    {
        if ($orderItem) {
            // Tek ürün iadesi
            $unitPrice = $orderItem->price;
            return $unitPrice * $quantity;
        }
        
        // Tüm sipariş iadesi
        return $order->total_amount;
    }
    
    /**
     * İade talebini onayla
     */
    public function approveReturn(ReturnRequest $returnRequest, array $data = []): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest, $data) {
            if (!$returnRequest->canBeApproved()) {
                throw new Exception('Bu iade talebi onaylanamaz.');
            }
            
            $oldStatus = $returnRequest->status;
            
            $returnRequest->update([
                'status' => ReturnRequest::STATUS_APPROVED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'admin_notes' => $data['admin_notes'] ?? $returnRequest->admin_notes,
                'refund_method' => $data['refund_method'] ?? ReturnRequest::REFUND_METHOD_ORIGINAL,
            ]);
            
            // Kargo etiketi oluştur (opsiyonel)
            if (!empty($data['generate_shipping_label'])) {
                $this->generateShippingLabel($returnRequest);
            }
            
            $this->logAction($returnRequest, ReturnLog::ACTION_APPROVED, $oldStatus, ReturnRequest::STATUS_APPROVED, $data['notes'] ?? 'İade talebi onaylandı.');
            
            return $returnRequest->fresh();
        });
    }
    
    /**
     * İade talebini reddet
     */
    public function rejectReturn(ReturnRequest $returnRequest, string $reason, array $data = []): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest, $reason, $data) {
            if (!$returnRequest->canBeRejected()) {
                throw new Exception('Bu iade talebi reddedilemez.');
            }
            
            $oldStatus = $returnRequest->status;
            
            $returnRequest->update([
                'status' => ReturnRequest::STATUS_REJECTED,
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $reason,
                'admin_notes' => $data['admin_notes'] ?? $returnRequest->admin_notes,
            ]);
            
            $this->logAction($returnRequest, ReturnLog::ACTION_REJECTED, $oldStatus, ReturnRequest::STATUS_REJECTED, $reason);
            
            return $returnRequest->fresh();
        });
    }
    
    /**
     * Ürün gönderildi olarak işaretle
     */
    public function markAsShipped(ReturnRequest $returnRequest, array $data): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest, $data) {
            if ($returnRequest->status !== ReturnRequest::STATUS_APPROVED) {
                throw new Exception('Sadece onaylanmış iadeler gönderilebilir.');
            }
            
            $oldStatus = $returnRequest->status;
            
            $returnRequest->update([
                'status' => ReturnRequest::STATUS_SHIPPED,
                'tracking_number' => $data['tracking_number'],
                'shipping_carrier' => $data['shipping_carrier'] ?? null,
            ]);
            
            $this->logAction($returnRequest, ReturnLog::ACTION_SHIPPED, $oldStatus, ReturnRequest::STATUS_SHIPPED, 'Ürün gönderildi. Takip No: ' . $data['tracking_number']);
            
            return $returnRequest->fresh();
        });
    }
    
    /**
     * Ürün teslim alındı olarak işaretle
     */
    public function markAsReceived(ReturnRequest $returnRequest, array $data = []): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest, $data) {
            if ($returnRequest->status !== ReturnRequest::STATUS_SHIPPED) {
                throw new Exception('Sadece gönderilmiş iadeler teslim alınabilir.');
            }
            
            $oldStatus = $returnRequest->status;
            
            $returnRequest->update([
                'status' => ReturnRequest::STATUS_RECEIVED,
                'received_at' => now(),
                'vendor_notes' => $data['vendor_notes'] ?? $returnRequest->vendor_notes,
            ]);
            
            $this->logAction($returnRequest, ReturnLog::ACTION_RECEIVED, $oldStatus, ReturnRequest::STATUS_RECEIVED, 'Ürün teslim alındı.');
            
            return $returnRequest->fresh();
        });
    }
    
    /**
     * Ürün incelendi olarak işaretle
     */
    public function markAsInspected(ReturnRequest $returnRequest, array $data): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest, $data) {
            if ($returnRequest->status !== ReturnRequest::STATUS_RECEIVED) {
                throw new Exception('Sadece teslim alınmış ürünler incelenebilir.');
            }
            
            $oldStatus = $returnRequest->status;
            
            $returnRequest->update([
                'status' => ReturnRequest::STATUS_INSPECTING,
                'inspected_at' => now(),
                'inspection_notes' => $data['inspection_notes'] ?? null,
            ]);
            
            $this->logAction($returnRequest, ReturnLog::ACTION_INSPECTED, $oldStatus, ReturnRequest::STATUS_INSPECTING, $data['inspection_notes'] ?? 'Ürün incelendi.');
            
            // İnceleme sonucu olumluysa para iadesini başlat
            if (!empty($data['approve_refund']) && $data['approve_refund']) {
                $this->initiateRefund($returnRequest);
            }
            
            return $returnRequest->fresh();
        });
    }
    
    /**
     * Para iadesini başlat
     */
    public function initiateRefund(ReturnRequest $returnRequest, array $data = []): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest, $data) {
            $returnRequest->update([
                'refund_status' => ReturnRequest::REFUND_PROCESSING,
            ]);
            
            $this->logAction($returnRequest, ReturnLog::ACTION_REFUND_INITIATED, null, null, 'Para iadesi başlatıldı.');
            
            // Para iadesi yöntemine göre işlem yap
            try {
                $refundMethod = $data['refund_method'] ?? $returnRequest->refund_method ?? ReturnRequest::REFUND_METHOD_WALLET;
                
                switch ($refundMethod) {
                    case ReturnRequest::REFUND_METHOD_WALLET:
                        $this->refundToWallet($returnRequest);
                        break;
                    case ReturnRequest::REFUND_METHOD_ORIGINAL:
                        $this->refundToOriginalPayment($returnRequest);
                        break;
                    case ReturnRequest::REFUND_METHOD_BANK:
                        // Banka transferi için manuel işlem gerekli
                        $returnRequest->update(['refund_status' => ReturnRequest::REFUND_PENDING]);
                        break;
                }
            } catch (Exception $e) {
                Log::error('Refund failed: ' . $e->getMessage(), ['return_id' => $returnRequest->id]);
                $returnRequest->update(['refund_status' => ReturnRequest::REFUND_FAILED]);
                $this->logAction($returnRequest, ReturnLog::ACTION_REFUND_FAILED, null, null, 'Para iadesi başarısız: ' . $e->getMessage());
                throw $e;
            }
            
            return $returnRequest->fresh();
        });
    }
    
    /**
     * Cüzdana para iadesi
     */
    protected function refundToWallet(ReturnRequest $returnRequest): void
    {
        // Cüzdan modülünü kullan
        $user = $returnRequest->user;
        
        // Wallet transaction oluştur
        if (class_exists(\App\Modules\Wallet\Models\Wallet::class)) {
            $wallet = $user->wallet ?? $user->wallets()->firstOrCreate(['currency' => 'TRY']);
            
            $wallet->increment('balance', $returnRequest->refund_amount);
            
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $returnRequest->refund_amount,
                'description' => "İade #" . $returnRequest->return_number,
                'reference_type' => ReturnRequest::class,
                'reference_id' => $returnRequest->id,
            ]);
        }
        
        $this->completeRefund($returnRequest);
    }
    
    /**
     * Orijinal ödeme yöntemine iade
     */
    protected function refundToOriginalPayment(ReturnRequest $returnRequest): void
    {
        $order = $returnRequest->order;
        $payment = $order->payments()->where('status', 'completed')->first();
        
        if (!$payment) {
            throw new Exception('Orijinal ödeme bulunamadı.');
        }
        
        // Ödeme servisini kullanarak iade yap
        $paymentGateway = $payment->payment_method ?? 'iyzico'; // default to iyzico
        
        try {
            switch ($paymentGateway) {
                case 'iyzico':
                    $this->refundViaIyzico($payment, $returnRequest->refund_amount);
                    break;
                case 'paytr':
                    $this->refundViaPayTR($payment, $returnRequest->refund_amount);
                    break;
                case 'stripe':
                    $this->refundViaStripe($payment, $returnRequest->refund_amount);
                    break;
                default:
                    throw new Exception('Desteklenmeyen ödeme yöntemi: ' . $paymentGateway);
            }
            
            // Log the refund
            Log::info('Refund processed successfully', [
                'return_id' => $returnRequest->id,
                'payment_id' => $payment->id,
                'amount' => $returnRequest->refund_amount,
                'gateway' => $paymentGateway
            ]);
            
            $this->completeRefund($returnRequest);
            
        } catch (Exception $e) {
            Log::error('Refund failed', [
                'return_id' => $returnRequest->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * İyzico ile iade işlemi
     */
    protected function refundViaIyzico($payment, float $amount): void
    {
        if (!class_exists(\Iyzipay\Options::class)) {
            throw new Exception('Iyzico SDK yüklü değil.');
        }
        
        $options = new \Iyzipay\Options();
        $options->setApiKey(config('services.iyzico.api_key'));
        $options->setSecretKey(config('services.iyzico.secret_key'));
        $options->setBaseUrl(config('services.iyzico.base_url', 'https://api.iyzipay.com'));

        $request = new \Iyzipay\Request\CreateRefundRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId($payment->transaction_id);
        $request->setPaymentTransactionId($payment->transaction_id);
        $request->setPrice($amount);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setIp($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

        $refund = \Iyzipay\Model\Refund::create($request, $options);

        if ($refund->getStatus() !== 'success') {
            throw new Exception('Iyzico iade başarısız: ' . $refund->getErrorMessage());
        }
    }
    
    /**
     * PayTR ile iade işlemi
     */
    protected function refundViaPayTR($payment, float $amount): void
    {
        $merchantId = config('services.paytr.merchant_id');
        $merchantKey = config('services.paytr.merchant_key');
        $merchantSalt = config('services.paytr.merchant_salt');
        
        if (!$merchantId || !$merchantKey || !$merchantSalt) {
            throw new Exception('PayTR yapılandırması eksik.');
        }
        
        $referenceNo = $payment->transaction_id;
        $amountInKurus = (int)($amount * 100); // Convert to kuruş
        
        // Generate hash
        $hashStr = $merchantId . $referenceNo . $amountInKurus . $merchantSalt;
        $token = base64_encode(hash_hmac('sha256', $hashStr, $merchantKey, true));
        
        $postData = [
            'merchant_id' => $merchantId,
            'reference_no' => $referenceNo,
            'amount' => $amountInKurus,
            'token' => $token
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/iade');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if (!isset($result['status']) || $result['status'] !== 'success') {
            $errorMessage = $result['reason'] ?? 'Bilinmeyen hata';
            throw new Exception('PayTR iade başarısız: ' . $errorMessage);
        }
    }
    
    /**
     * Stripe ile iade işlemi
     */
    protected function refundViaStripe($payment, float $amount): void
    {
        if (!class_exists(\Stripe\Stripe::class)) {
            throw new Exception('Stripe SDK yüklü değil.');
        }
        
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        
        try {
            \Stripe\Refund::create([
                'payment_intent' => $payment->transaction_id,
                'amount' => (int)($amount * 100), // Convert to cents
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Stripe iade başarısız: ' . $e->getMessage());
        }
    }
    
    /**
     * Para iadesini tamamla
     */
    public function completeRefund(ReturnRequest $returnRequest): ReturnRequest
    {
        $oldStatus = $returnRequest->status;
        
        $returnRequest->update([
            'refund_status' => ReturnRequest::REFUND_COMPLETED,
            'refunded_at' => now(),
            'status' => ReturnRequest::STATUS_REFUNDED,
        ]);
        
        $this->logAction($returnRequest, ReturnLog::ACTION_REFUND_COMPLETED, $oldStatus, ReturnRequest::STATUS_REFUNDED, 'Para iadesi tamamlandı. Tutar: ' . $returnRequest->refund_amount . ' TL');
        
        return $returnRequest->fresh();
    }
    
    /**
     * İade sürecini tamamla
     */
    public function completeReturn(ReturnRequest $returnRequest): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest) {
            if (!in_array($returnRequest->status, [ReturnRequest::STATUS_REFUNDED])) {
                throw new Exception('Bu iade henüz tamamlanamaz.');
            }
            
            $oldStatus = $returnRequest->status;
            
            $returnRequest->update([
                'status' => ReturnRequest::STATUS_COMPLETED,
            ]);
            
            $this->logAction($returnRequest, ReturnLog::ACTION_COMPLETED, $oldStatus, ReturnRequest::STATUS_COMPLETED, 'İade süreci tamamlandı.');
            
            return $returnRequest->fresh();
        });
    }
    
    /**
     * İade talebini iptal et
     */
    public function cancelReturn(ReturnRequest $returnRequest, string $reason = null): ReturnRequest
    {
        return DB::transaction(function () use ($returnRequest, $reason) {
            if (!$returnRequest->canBeCancelled()) {
                throw new Exception('Bu iade talebi iptal edilemez.');
            }
            
            $oldStatus = $returnRequest->status;
            
            $returnRequest->update([
                'status' => ReturnRequest::STATUS_CANCELLED,
            ]);
            
            $this->logAction($returnRequest, ReturnLog::ACTION_CANCELLED, $oldStatus, ReturnRequest::STATUS_CANCELLED, $reason ?? 'İade talebi iptal edildi.');
            
            return $returnRequest->fresh();
        });
    }
    
    /**
     * Kargo etiketi oluştur
     */
    protected function generateShippingLabel(ReturnRequest $returnRequest): void
    {
        try {
            // Get shipping service configuration
            $shippingCarrier = $returnRequest->shipping_carrier ?? config('shipping.default_carrier', 'aras');
            
            // Create PDF using dompdf
            $pdf = \PDF::loadView('pdfs.shipping-label', [
                'returnRequest' => $returnRequest,
                'order' => $returnRequest->order,
                'user' => $returnRequest->user,
                'vendor' => $returnRequest->vendor,
                'barcode' => $this->generateBarcode($returnRequest->return_number),
                'qrCode' => $this->generateQRCode($returnRequest->return_number),
            ]);
            
            // Ensure storage directory exists
            $labelDir = storage_path('app/public/return-labels');
            if (!file_exists($labelDir)) {
                mkdir($labelDir, 0755, true);
            }
            
            // Save PDF
            $filename = 'return-label-' . $returnRequest->return_number . '.pdf';
            $filepath = $labelDir . '/' . $filename;
            $pdf->save($filepath);
            
            // Update return request with label URL
            $labelUrl = '/storage/return-labels/' . $filename;
            $returnRequest->update(['shipping_label_url' => $labelUrl]);
            
            Log::info('Shipping label generated', [
                'return_id' => $returnRequest->id,
                'label_url' => $labelUrl
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to generate shipping label: ' . $e->getMessage(), [
                'return_id' => $returnRequest->id
            ]);
            // Don't throw - label generation is not critical
        }
    }
    
    /**
     * Generate barcode for return number
     */
    protected function generateBarcode(string $returnNumber): string
    {
        // Simple barcode representation (you can use a library like picqer/php-barcode-generator for real barcodes)
        return 'data:image/svg+xml;base64,' . base64_encode('
            <svg xmlns="http://www.w3.org/2000/svg" width="200" height="60">
                <rect width="200" height="60" fill="white"/>
                <text x="100" y="40" font-family="monospace" font-size="14" text-anchor="middle">' . $returnNumber . '</text>
            </svg>
        ');
    }
    
    /**
     * Generate QR code for return number
     */
    protected function generateQRCode(string $returnNumber): string
    {
        // Simple QR code representation using external service
        return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($returnNumber);
    }
    
    /**
     * Log kaydı oluştur
     */
    protected function logAction(ReturnRequest $returnRequest, string $action, ?string $fromStatus, ?string $toStatus, ?string $notes = null, array $metadata = []): ReturnLog
    {
        return ReturnLog::create([
            'return_request_id' => $returnRequest->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
            'metadata' => $metadata,
        ]);
    }
    
    /**
     * İstatistikler
     */
    public function getStatistics(?int $vendorId = null): array
    {
        $query = ReturnRequest::query();
        
        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }
        
        return [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', ReturnRequest::STATUS_PENDING)->count(),
            'approved' => (clone $query)->where('status', ReturnRequest::STATUS_APPROVED)->count(),
            'completed' => (clone $query)->where('status', ReturnRequest::STATUS_COMPLETED)->count(),
            'rejected' => (clone $query)->where('status', ReturnRequest::STATUS_REJECTED)->count(),
            'total_refunded' => (clone $query)->where('refund_status', ReturnRequest::REFUND_COMPLETED)->sum('refund_amount'),
            'by_reason' => (clone $query)->selectRaw('reason_category, COUNT(*) as count')
                ->groupBy('reason_category')
                ->pluck('count', 'reason_category')
                ->toArray(),
        ];
    }
}
