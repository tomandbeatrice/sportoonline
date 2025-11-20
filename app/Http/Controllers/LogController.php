<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function index()
    {
        $path = storage_path('logs/errors.log');

        if (!File::exists($path)) {
            return response()->json(['message' => 'Log dosyası bulunamadı'], 404);
        }

        $content = File::get($path);
        $lines = explode("\n", $content);

        $logs = collect($lines)
            ->filter(fn($line) => !empty($line))
            ->map(fn($line) => [
                'timestamp' => substr($line, 0, 19),
                'message' => substr($line, 20),
            ])
            ->values();

        return response()->json($logs);
    }
}