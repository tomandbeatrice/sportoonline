<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transfer;
    public $previousStatus;
    public $newStatus;

    public function __construct($transfer, string $previousStatus, string $newStatus)
    {
        $this->transfer = $transfer;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('transfers.' . $this->transfer->user_id),
            new Channel('admin.transfers'),
        ];

        if ($this->transfer->driver_id) {
            $channels[] = new PrivateChannel('driver.transfers.' . $this->transfer->driver_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'transfer.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'transfer_id' => $this->transfer->id,
            'booking_number' => $this->transfer->booking_number,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'pickup_location' => $this->transfer->pickup_location,
            'dropoff_location' => $this->transfer->dropoff_location,
            'pickup_datetime' => $this->transfer->pickup_datetime->toIso8601String(),
            'driver_name' => $this->transfer->driver?->full_name,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
