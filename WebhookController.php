<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class WebhookController extends Controller
{
    /**
     * Sanal POS servisinden gelen ödeme bildirimini işler.
     * Başarılı ödeme sonrası sipariş durumu 'confirmed' yapılır.
     */
    public function handle(Request $request)
    {
        $conversationId = $request->input('conversationId');
        $status = $request->input('status');

        $order = Order::find($conversationId);

        if ($order && $status === 'success') {
            $order->status = 'confirmed';
            $order->save();

            $order->logs()->create([
                'message' => 'Ödeme başarılı, sipariş onaylandı.',
                'created_at' => now()
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}