<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function store(Request $request) {
        $path = $request->file('image')->store('banners', 'public');
        Storage::disk('local')->put('banner_' . $request->position . '.json', json_encode([
            'image' => $path,
            'position' => $request->position
        ]));
        return response()->json(['message' => 'Banner kaydedildi']);
    }
}