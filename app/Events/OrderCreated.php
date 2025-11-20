<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $message;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->message = "Yeni sipariş oluşturuldu: #{$order->id}";
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('orders'),
            new PrivateChannel('seller.' . $this->order->items->first()->product->seller_id),
            new PrivateChannel('admin'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'total' => $this->order->total,
            'status' => $this->order->status,
            'message' => $this->message,
            'created_at' => $this->order->created_at->toISOString(),
        ];
    }
}
