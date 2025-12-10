<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardStatsUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stats;
    public $type;

    public function __construct(array $stats, string $type = 'all')
    {
        $this->stats = $stats;
        $this->type = $type;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin.dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'dashboard.stats.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
            'stats' => $this->stats,
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
