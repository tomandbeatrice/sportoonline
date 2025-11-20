<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $amount;
    public $sellerId;
    public $message;

    public function __construct(int $orderId, float $amount, int $sellerId)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->sellerId = $sellerId;
        $this->message = "Sipariş #{$orderId} için ödeme alındı: ₺" . number_format($amount, 2);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('seller.' . $this->sellerId),
            new PrivateChannel('admin'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
            'amount' => $this->amount,
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
        ];
    }
}
