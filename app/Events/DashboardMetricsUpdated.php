<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardMetricsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $metrics;
    public string $dashboardType;

    public function __construct(array $metrics, string $dashboardType = 'admin')
    {
        $this->metrics = $metrics;
        $this->dashboardType = $dashboardType;
    }

    public function broadcastOn()
    {
        return new Channel('dashboard.' . $this->dashboardType);
    }

    public function broadcastAs()
    {
        return 'metrics.updated';
    }

    public function broadcastWith()
    {
        return [
            'metrics' => $this->metrics,
            'timestamp' => now()->toISOString(),
        ];
    }
}
