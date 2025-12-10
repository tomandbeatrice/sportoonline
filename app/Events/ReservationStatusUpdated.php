<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reservation;
    public $previousStatus;
    public $newStatus;

    public function __construct($reservation, string $previousStatus, string $newStatus)
    {
        $this->reservation = $reservation;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('reservations.' . $this->reservation->user_id),
            new PrivateChannel('hotel.reservations.' . $this->reservation->hotel_id),
            new Channel('admin.reservations'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reservation.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'reservation_number' => $this->reservation->reservation_number,
            'hotel_name' => $this->reservation->hotel->name ?? null,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'check_in' => $this->reservation->check_in->format('Y-m-d'),
            'check_out' => $this->reservation->check_out->format('Y-m-d'),
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
