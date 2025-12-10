<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = strtolower($request->message);
        $response = "Bunu henüz anlayamıyorum ama öğreniyorum!";

        // Simple keyword matching logic (Placeholder for LLM integration)
        if (str_contains($userMessage, 'sipariş')) {
            $response = 'Siparişlerinizle ilgili detayları "Hesabım > Siparişlerim" sayfasından görebilirsiniz.';
        } elseif (str_contains($userMessage, 'yemek')) {
            $response = 'Acıktınız mı? Hemen size en yakın restoranları listeleyebilirim. "Keşfet" sayfasına göz atın!';
        } elseif (str_contains($userMessage, 'merhaba') || str_contains($userMessage, 'selam')) {
            $response = 'Merhaba! Bugün size nasıl yardımcı olabilirim?';
        } elseif (str_contains($userMessage, 'puan') || str_contains($userMessage, 'seviye')) {
            $response = 'Puan durumunuzu sağ üst köşedeki rozetten takip edebilirsiniz. Alışveriş yaptıkça puan kazanırsınız!';
        }

        return response()->json([
            'response' => $response,
            'timestamp' => now()
        ]);
    }
}
