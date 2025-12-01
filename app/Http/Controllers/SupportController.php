<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function sendMessage(Request $request)
    {
        broadcast(new SupportMessageSent($request->user(), $request->message));
        return response()->json(['message' => 'Mesaj gönderildi']);
    }
}