<?php

namespace App\Traits;

use App\Models\OrderLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    public function logOrderActivity($order, array $data = [])
    {
        OrderLog::create([
            'order_id'     => $order->id,
            'admin_id'     => Auth::id(),
            'old_status'   => $data['old_status'] ?? null,
            'new_status'   => $data['new_status'] ?? null,
            'action_type'  => $data['action_type'] ?? null,
            'note'         => $data['note'] ?? null,
        ]);
    }
}