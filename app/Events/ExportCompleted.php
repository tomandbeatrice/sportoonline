<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $filename;
    public int $recordCount;
    public string $exportType;
    public ?int $userId;

    public function __construct(string $filename, int $recordCount, string $exportType = 'general', ?int $userId = null)
    {
        $this->filename = $filename;
        $this->recordCount = $recordCount;
        $this->exportType = $exportType;
        $this->userId = $userId ?? auth()->id();
    }

    public function broadcastOn()
    {
        return $this->userId 
            ? new Channel('user.' . $this->userId . '.exports')
            : new Channel('exports.completed');
    }

    public function broadcastAs()
    {
        return 'export.completed';
    }

    public function broadcastWith()
    {
        return [
            'filename' => $this->filename,
            'record_count' => $this->recordCount,
            'export_type' => $this->exportType,
            'timestamp' => now()->toISOString(),
            'download_url' => route('admin.export.files.download', ['file' => $this->filename]),
        ];
    }
}
