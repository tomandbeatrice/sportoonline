<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Mesaj gönder
    public function store(Request $request)
    {
        $message = Message::create([
            'from_id' => auth()->id(),
            'to_id' => $request->to_id,
            'body' => $request->body,
            'is_read' => false,
        ]);
        return response()->json($message);
    }

    // Mesajları listele
    public function index(Request $request)
    {
        $messages = Message::where(function($q) {
            $q->where('from_id', auth()->id())
              ->orWhere('to_id', auth()->id());
        })->orderByDesc('created_at')->get();
        return response()->json($messages);
    }

    // Mesajı okundu olarak işaretle
    public function markRead(Message $message)
    {
        if ($message->to_id === auth()->id()) {
            $message->is_read = true;
            $message->save();
        }
        return response()->json(['success' => true]);
    }
}
