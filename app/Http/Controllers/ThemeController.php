<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThemeController extends Controller
{
    /**
     * Get current theme settings
     */
    public function index()
    {
        $theme = Cache::get('theme_settings', $this->getDefaultTheme());
        return response()->json($theme);
    }

    /**
     * Store/Update theme settings
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'logo_url' => 'nullable|string|max:500',
            'favicon_url' => 'nullable|string|max:500',
            'site_name' => 'nullable|string|max:100',
            'font_family' => 'nullable|string|max:100',
        ]);

        $theme = array_merge($this->getDefaultTheme(), $validated);
        Cache::put('theme_settings', $theme, now()->addDays(30));

        return response()->json([
            'message' => 'Tema ayarları kaydedildi',
            'theme' => $theme
        ]);
    }

    /**
     * Default theme settings
     */
    private function getDefaultTheme(): array
    {
        return [
            'primary_color' => '#2563eb',
            'secondary_color' => '#1e40af',
            'logo_url' => '/images/logo.png',
            'favicon_url' => '/favicon.ico',
            'site_name' => 'SportoOnline',
            'font_family' => 'Inter, sans-serif',
        ];
    }
}
