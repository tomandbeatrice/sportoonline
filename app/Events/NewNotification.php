<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $userId;

    public function __construct(array $notification, int $userId)
    {
        $this->notification = $notification;
        $this->userId = $userId;
    }

    public function broadcastOn(): array
    {
        return [
            new \Illuminate\Broadcasting\PrivateChannel('notifications.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.notification';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification['id'] ?? uniqid(),
            'type' => $this->notification['type'] ?? 'info',
            'title' => $this->notification['title'],
            'message' => $this->notification['message'],
            'icon' => $this->notification['icon'] ?? null,
            'url' => $this->notification['url'] ?? null,
            'data' => $this->notification['data'] ?? [],
            'created_at' => now()->toIso8601String(),
        ];
    }
}
