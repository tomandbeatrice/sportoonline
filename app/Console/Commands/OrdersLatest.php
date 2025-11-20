<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OrdersLatest extends Command
{
    protected $signature = 'orders:latest';
    protected $description = 'List the latest orders from the system';

    public function handle()
    {
        $orders = \App\Models\Order::latest()->take(10)->get();

        if ($orders->isEmpty()) {
            $this->info('No recent orders found.');
            return;
        }

        $this->table(
            ['ID', 'Customer', 'Total', 'Status', 'Created At'],
            $orders->map(function ($order) {
                return [
                    $order->id,
                    $order->customer_name,
                    $order->total_price,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i'),
                ];
            })->toArray()
        );
    }
}