<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FoodOrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $previousStatus;
    public $newStatus;

    public function __construct($order, string $previousStatus, string $newStatus)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('food-orders.' . $this->order->user_id),
            new PrivateChannel('restaurant.orders.' . $this->order->restaurant_id),
            new Channel('admin.food-orders'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'food.order.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'restaurant_name' => $this->order->restaurant->name ?? null,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'total' => $this->order->total,
            'estimated_delivery_time' => $this->order->estimated_delivery_time,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
