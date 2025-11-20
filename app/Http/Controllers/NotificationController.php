<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    // ✅ Satıcıya özel kampanya bildirimini oluşturan method
    public function storeCampaignNotification($sellerId, Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'message' => 'required|string'
        ]);

        Notification::create([
            'seller_id' => $sellerId,
            'title' => $validated['title'],
            'message' => $validated['message'],
            'read_at' => null
        ]);

        return response()->json(['status' => 'sent']);
    }
}