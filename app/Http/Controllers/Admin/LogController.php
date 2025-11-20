<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;

class LogController
{
    public function live(): JsonResponse
    {
        $path = storage_path('logs/laravel.log');

        if (!File::exists($path)) {
            return response()->json(['error' => 'Log dosyası bulunamadı'], 404);
        }

        $lines = explode("\n", File::get($path));

        $filtered = collect($lines)
            ->filter(fn($line) => str_contains($line, 'ERROR') || str_contains($line, 'WARNING') || str_contains($line, 'INFO'))
            ->take(-50)
            ->values();

        return response()->json($filtered);
    }
}