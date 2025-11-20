<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use Illuminate\Http\Request;

class AdminThemeController extends Controller
{
    public function index()
    {
        $theme = Theme::first();
        
        if (!$theme) {
            return response()->json($this->getDefaultTheme());
        }

        return response()->json($theme->settings);
    }

    public function update(Request $request)
    {
        $theme = Theme::firstOrCreate([]);
        $theme->settings = $request->all();
        $theme->save();

        // Generate CSS file
        $this->generateCssFile($request->all());

        return response()->json(['message' => 'Tema kaydedildi']);
    }

    private function generateCssFile($settings)
    {
        $css = ":root {\n";
        $css .= "  --primary-color: {$settings['colors']['primary']};\n";
        $css .= "  --secondary-color: {$settings['colors']['secondary']};\n";
        $css .= "  --accent-color: {$settings['colors']['accent']};\n";
        $css .= "  --success-color: {$settings['colors']['success']};\n";
        $css .= "  --danger-color: {$settings['colors']['danger']};\n";
        $css .= "  --warning-color: {$settings['colors']['warning']};\n";
        $css .= "  --heading-font: '{$settings['typography']['headingFont']}';\n";
        $css .= "  --body-font: '{$settings['typography']['bodyFont']}';\n";
        $css .= "  --h1-size: {$settings['typography']['sizes']['h1']}px;\n";
        $css .= "  --h2-size: {$settings['typography']['sizes']['h2']}px;\n";
        $css .= "  --h3-size: {$settings['typography']['sizes']['h3']}px;\n";
        $css .= "  --body-size: {$settings['typography']['sizes']['body']}px;\n";
        $css .= "  --small-size: {$settings['typography']['sizes']['small']}px;\n";
        $css .= "  --border-radius: {$settings['advanced']['borderRadius']}px;\n";
        $css .= "}\n\n";

        // Custom CSS
        if (!empty($settings['customCss'])) {
            $css .= "\n/* Custom CSS */\n";
            $css .= $settings['customCss'];
        }

        // Save to public directory
        file_put_contents(public_path('css/theme.css'), $css);
    }

    private function getDefaultTheme()
    {
        return [
            'colors' => [
                'primary' => '#2563eb',
                'secondary' => '#64748b',
                'accent' => '#f59e0b',
                'success' => '#10b981',
                'danger' => '#ef4444',
                'warning' => '#f59e0b'
            ],
            'typography' => [
                'headingFont' => 'Poppins',
                'bodyFont' => 'Inter',
                'sizes' => [
                    'h1' => 48,
                    'h2' => 36,
                    'h3' => 28,
                    'body' => 16,
                    'small' => 14
                ]
            ],
            'layout' => [
                'homepage' => [
                    ['id' => 1, 'name' => 'Hero Banner', 'description' => 'Ana görsel slider', 'enabled' => true],
                    ['id' => 2, 'name' => 'Kampanyalar', 'description' => 'Aktif kampanyalar', 'enabled' => true],
                    ['id' => 3, 'name' => 'Öne Çıkan Ürünler', 'description' => 'Featured products', 'enabled' => true],
                    ['id' => 4, 'name' => 'Kategoriler', 'description' => 'Ürün kategorileri', 'enabled' => true],
                    ['id' => 5, 'name' => 'Yeni Ürünler', 'description' => 'Son eklenen ürünler', 'enabled' => true],
                    ['id' => 6, 'name' => 'En Çok Satanlar', 'description' => 'Best sellers', 'enabled' => true],
                    ['id' => 7, 'name' => 'Blog Yazıları', 'description' => 'Son blog gönderileri', 'enabled' => false],
                    ['id' => 8, 'name' => 'Markalar', 'description' => 'Partner markalar', 'enabled' => true]
                ],
                'headerType' => 'sticky',
                'footerType' => 'default',
                'showBreadcrumbs' => true,
                'showSidebar' => false,
                'maxWidth' => '1400px'
            ],
            'customCss' => '',
            'advanced' => [
                'borderRadius' => 8,
                'shadowIntensity' => 'md',
                'animationSpeed' => 'normal',
                'enableAnimations' => true,
                'enableParallax' => false,
                'darkModeSupport' => false
            ]
        ];
    }
}
