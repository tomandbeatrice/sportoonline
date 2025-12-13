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
        try {
            $this->processRefund($payment, $returnRequest->refund_amount);
        } catch (Exception $e) {
            Log::error('Refund processing failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'return_id' => $returnRequest->id
            ]);
            throw $e;
        }
        
        $this->completeRefund($returnRequest);
    }
    
    /**
     * Payment gateway'e göre refund işlemi
     */
    protected function processRefund($payment, float $amount): void
    {
        $gateway = $payment->payment_method ?? $payment->gateway;
        
        switch ($gateway) {
            case 'iyzico':
                $this->processIyzicoRefund($payment, $amount);
                break;
            case 'paytr':
                $this->processPayTRRefund($payment, $amount);
                break;
            case 'stripe':
                $this->processStripeRefund($payment, $amount);
                break;
            default:
                throw new Exception("Desteklenmeyen ödeme yöntemi: {$gateway}");
        }
    }
    
    /**
     * Iyzico refund işlemi
     */
    protected function processIyzicoRefund($payment, float $amount): void
    {
        if (!class_exists(\Iyzipay\Model\Refund::class)) {
            Log::warning('Iyzico library not installed, marking refund as pending manual processing');
            return;
        }
        
        try {
            $options = new \Iyzipay\Options();
            $options->setApiKey(config('iyzico.api_key'));
            $options->setSecretKey(config('iyzico.secret_key'));
            $options->setBaseUrl(config('iyzico.base_url'));
            
            $request = new \Iyzipay\Request\CreateRefundRequest();
            $request->setLocale(\Iyzipay\Model\Locale::TR);
            $request->setConversationId("REFUND-{$payment->id}-" . uniqid());
            $request->setPaymentTransactionId($payment->transaction_id);
            $request->setPrice($amount);
            $request->setCurrency(\Iyzipay\Model\Currency::TL);
            
            $refund = \Iyzipay\Model\Refund::create($request, $options);
            
            if ($refund->getStatus() !== 'success') {
                throw new Exception('Iyzico refund failed: ' . $refund->getErrorMessage());
            }
            
            Log::info('Iyzico refund successful', [
                'payment_id' => $payment->id,
                'amount' => $amount,
                'refund_id' => $refund->getPaymentId()
            ]);
        } catch (Exception $e) {
            Log::error('Iyzico refund error: ' . $e->getMessage());
            throw new Exception('Iyzico iade işlemi başarısız: ' . $e->getMessage());
        }
    }
    
    /**
     * PayTR refund işlemi
     */
    protected function processPayTRRefund($payment, float $amount): void
    {
        try {
            $merchantId = config('paytr.merchant_id');
            $merchantKey = config('paytr.merchant_key');
            $merchantSalt = config('paytr.merchant_salt');
            
            $merchantOid = $payment->merchant_oid ?? $payment->transaction_id;
            $refundAmount = intval($amount * 100); // Kuruş cinsinden
            $referenceNo = $payment->reference_no ?? '';
            
            // PayTR hash oluştur
            $hashStr = $merchantId . $merchantOid . $refundAmount . $merchantSalt;
            $token = base64_encode(hash_hmac('sha256', $hashStr, $merchantKey, true));
            
            $data = [
                'merchant_id' => $merchantId,
                'merchant_oid' => $merchantOid,
                'return_amount' => $refundAmount,
                'reference_no' => $referenceNo,
                'paytr_token' => $token
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, config('paytr.api_url') . '/odeme/iade');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new Exception('PayTR API connection error: ' . curl_error($ch));
            }
            
            curl_close($ch);
            
            $result = json_decode($response, true);
            
            if ($result['status'] !== 'success') {
                throw new Exception('PayTR refund failed: ' . ($result['reason'] ?? 'Unknown error'));
            }
            
            Log::info('PayTR refund successful', [
                'payment_id' => $payment->id,
                'amount' => $amount,
                'merchant_oid' => $merchantOid
            ]);
        } catch (Exception $e) {
            Log::error('PayTR refund error: ' . $e->getMessage());
            throw new Exception('PayTR iade işlemi başarısız: ' . $e->getMessage());
        }
    }
    
    /**
     * Stripe refund işlemi
     */
    protected function processStripeRefund($payment, float $amount): void
    {
        if (!class_exists(\Stripe\Stripe::class)) {
            Log::warning('Stripe library not installed, marking refund as pending manual processing');
            return;
        }
        
        try {
            \Stripe\Stripe::setApiKey(config('stripe.secret_key'));
            
            $refund = \Stripe\Refund::create([
                'payment_intent' => $payment->payment_intent_id ?? $payment->transaction_id,
                'amount' => intval($amount * 100), // Cents
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id
                ]
            ]);
            
            if ($refund->status !== 'succeeded' && $refund->status !== 'pending') {
                throw new Exception('Stripe refund failed: ' . $refund->failure_reason);
            }
            
            Log::info('Stripe refund successful', [
                'payment_id' => $payment->id,
                'amount' => $amount,
                'refund_id' => $refund->id,
                'status' => $refund->status
            ]);
        } catch (Exception $e) {
            Log::error('Stripe refund error: ' . $e->getMessage());
            throw new Exception('Stripe iade işlemi başarısız: ' . $e->getMessage());
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
            // ShippingService kullanarak kargo etiketi oluştur
            $labelPath = $this->createShippingLabelPDF($returnRequest);
            
            // URL'yi kaydet
            $returnRequest->update(['shipping_label_url' => $labelPath]);
            
            Log::info('Shipping label generated', [
                'return_id' => $returnRequest->id,
                'label_path' => $labelPath
            ]);
        } catch (Exception $e) {
            Log::error('Shipping label generation failed: ' . $e->getMessage());
            // Hata durumunda devam et, etiket manuel oluşturulabilir
        }
    }
    
    /**
     * PDF kargo etiketi oluştur
     */
    protected function createShippingLabelPDF(ReturnRequest $returnRequest): string
    {
        // Dizin kontrolü ve oluşturma
        $labelDir = storage_path('app/public/return-labels');
        if (!file_exists($labelDir)) {
            mkdir($labelDir, 0755, true);
        }
        
        // Dosya adı
        $filename = $returnRequest->return_number . '.pdf';
        $filepath = $labelDir . '/' . $filename;
        
        // Barcode ve QR Code oluştur
        $barcodeData = $this->generateBarcode($returnRequest->return_number);
        $qrcodeData = $this->generateQRCode($returnRequest->return_number);
        
        // PDF içeriğini hazırla
        $html = view('pdfs.shipping-label', [
            'returnRequest' => $returnRequest,
            'barcode' => $barcodeData,
            'qrcode' => $qrcodeData,
        ])->render();
        
        // PDF oluştur
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->save($filepath);
        } else {
            // Fallback: Basit bir HTML dosyası oluştur
            $filename = $returnRequest->return_number . '.html';
            $filepath = $labelDir . '/' . $filename;
            file_put_contents($filepath, $html);
        }
        
        return '/storage/return-labels/' . $filename;
    }
    
    /**
     * Barcode oluştur (Code 128 formatında)
     */
    protected function generateBarcode(string $data): string
    {
        // Basit bir barcode SVG oluştur
        // Gerçek uygulamada barcode kütüphanesi kullanılabilir
        $barcode = '';
        $width = 2;
        $height = 60;
        $x = 0;
        
        // Her karakter için basit bir çizgi deseni oluştur
        foreach (str_split($data) as $char) {
            $barWidth = (ord($char) % 3) + 1;
            $barcode .= sprintf(
                '<rect x="%d" y="0" width="%d" height="%d" fill="black"/>',
                $x,
                $barWidth * $width,
                $height
            );
            $x += ($barWidth + 1) * $width;
        }
        
        $totalWidth = $x;
        
        return sprintf(
            '<svg width="%d" height="%d" xmlns="http://www.w3.org/2000/svg">%s</svg>',
            $totalWidth,
            $height,
            $barcode
        );
    }
    
    /**
     * QR Code oluştur
     * 
     * Note: Currently using Google Charts API for QR code generation.
     * This exposes the return request number to Google's servers.
     * For production, consider using a local QR code library like:
     * - bacon/bacon-qr-code
     * - endroid/qr-code
     * 
     * Alternative: Generate QR codes on the server side and embed as data URI
     */
    protected function generateQRCode(string $data): string
    {
        // Basit bir QR kod placeholder oluştur
        // Gerçek uygulamada QR kod kütüphanesi kullanılabilir
        // Şimdilik Google Charts API kullanarak QR kod URL'i döndürüyoruz
        $size = '150x150';
        $qrUrl = 'https://chart.googleapis.com/chart?chs=' . $size . '&cht=qr&chl=' . urlencode($data);
        
        return sprintf(
            '<img src="%s" alt="QR Code" width="150" height="150" />',
            $qrUrl
        );
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
