<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function seller($id)
    {
        $total = Order::where('seller_id', $id)->count();
        $completed = Order::where('seller_id', $id)->where('status', 'tamamlandı')->count();
        $revenue = Order::where('seller_id', $id)->sum('price');

        return response()->json([
            'totalOrders' => $total,
            'completedOrders' => $completed,
            'completionRate' => $total ? round($completed / $total * 100, 2) : 0,
            'revenue' => $revenue
        ]);
    }
}
