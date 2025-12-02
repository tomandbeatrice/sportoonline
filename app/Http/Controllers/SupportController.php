<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Note: SupportMessageSent event needs to be created if not exists
        // broadcast(new SupportMessageSent($request->user(), $request->message));
        return response()->json(['message' => 'Mesaj gönderildi']);
    }
}
