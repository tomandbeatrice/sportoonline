<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_gateway_id',
        'transaction_id',
        'conversation_id',
        'status',
        'amount',
        'currency',
        'request_data',
        'response_data',
        'error_message',
        'error_code',
        'paid_at',
        'refunded_at',
        'refund_amount',
        'refund_reason',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the payment gateway
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    /**
     * Mark as successful
     */
    public function markAsSuccess(string $transactionId, array $responseData = []): void
    {
        $this->update([
            'status' => 'success',
            'transaction_id' => $transactionId,
            'response_data' => $responseData,
            'paid_at' => now(),
            'error_message' => null,
            'error_code' => null,
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $errorMessage, ?string $errorCode = null, array $responseData = []): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'error_code' => $errorCode,
            'response_data' => $responseData,
        ]);
    }

    /**
     * Mark as refunded
     */
    public function markAsRefunded(float $amount, ?string $reason = null): void
    {
        $this->update([
            'status' => 'refunded',
            'refund_amount' => $amount,
            'refund_reason' => $reason,
            'refunded_at' => now(),
        ]);
    }
}
