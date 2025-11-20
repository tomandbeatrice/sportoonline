<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'general' => [
                'site_name' => $this->getSetting('site_name', 'SportOnline'),
                'site_url' => $this->getSetting('site_url', config('app.url')),
                'site_description' => $this->getSetting('site_description', ''),
                'support_email' => $this->getSetting('support_email', ''),
                'support_phone' => $this->getSetting('support_phone', ''),
                'logo_url' => $this->getSetting('logo_url', ''),
                'favicon_url' => $this->getSetting('favicon_url', ''),
                'currency' => $this->getSetting('currency', 'TRY'),
                'vat_rate' => (float) $this->getSetting('vat_rate', 20),
                'min_order_amount' => (float) $this->getSetting('min_order_amount', 0),
                'free_shipping_limit' => (float) $this->getSetting('free_shipping_limit', 150),
            ],
            'payment' => [
                'iyzico' => json_decode($this->getSetting('payment_iyzico', '{"enabled":false,"api_key":"","secret_key":"","mode":"sandbox"}'), true),
                'paytr' => json_decode($this->getSetting('payment_paytr', '{"enabled":false,"merchant_id":"","merchant_key":"","merchant_salt":""}'), true),
                'mokapos' => json_decode($this->getSetting('payment_mokapos', '{"enabled":false,"dealer_code":"","username":"","password":""}'), true),
            ],
            'shipping' => [
                'aras' => json_decode($this->getSetting('shipping_aras', '{"enabled":false,"customer_code":"","username":"","password":"","price":15}'), true),
                'yurtici' => json_decode($this->getSetting('shipping_yurtici', '{"enabled":false,"customer_no":"","api_key":"","price":15}'), true),
                'mng' => json_decode($this->getSetting('shipping_mng', '{"enabled":false,"username":"","password":"","price":15}'), true),
            ],
            'email' => [
                'smtp_host' => $this->getSetting('smtp_host', ''),
                'smtp_port' => (int) $this->getSetting('smtp_port', 587),
                'smtp_username' => $this->getSetting('smtp_username', ''),
                'smtp_password' => $this->getSetting('smtp_password', ''),
                'from_name' => $this->getSetting('from_name', ''),
                'from_email' => $this->getSetting('from_email', ''),
            ],
            'commission' => [
                'default_rate' => (float) $this->getSetting('commission_default_rate', 10),
                'allow_seller_specific' => (bool) $this->getSetting('commission_allow_seller_specific', true),
                'categories' => json_decode($this->getSetting('commission_categories', '[]'), true),
            ],
            'maintenance' => [
                'enabled' => (bool) $this->getSetting('maintenance_enabled', false),
                'message' => $this->getSetting('maintenance_message', ''),
                'estimated_end' => $this->getSetting('maintenance_estimated_end', ''),
                'allowed_ips' => $this->getSetting('maintenance_allowed_ips', ''),
            ],
        ];

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $settings = $request->all();

        // General settings
        if (isset($settings['general'])) {
            foreach ($settings['general'] as $key => $value) {
                $this->setSetting($key, $value);
            }
        }

        // Payment settings
        if (isset($settings['payment'])) {
            foreach ($settings['payment'] as $gateway => $config) {
                $this->setSetting('payment_' . $gateway, json_encode($config));
            }
        }

        // Shipping settings
        if (isset($settings['shipping'])) {
            foreach ($settings['shipping'] as $company => $config) {
                $this->setSetting('shipping_' . $company, json_encode($config));
            }
        }

        // Email settings
        if (isset($settings['email'])) {
            foreach ($settings['email'] as $key => $value) {
                $this->setSetting($key, $value);
            }
        }

        // Commission settings
        if (isset($settings['commission'])) {
            foreach ($settings['commission'] as $key => $value) {
                $settingKey = 'commission_' . $key;
                $this->setSetting($settingKey, is_array($value) ? json_encode($value) : $value);
            }
        }

        // Maintenance settings
        if (isset($settings['maintenance'])) {
            foreach ($settings['maintenance'] as $key => $value) {
                $this->setSetting('maintenance_' . $key, $value);
            }
        }

        return response()->json([
            'message' => 'Ayarlar kaydedildi'
        ]);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo
            $oldLogo = $this->getSetting('logo_url');
            if ($oldLogo) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $oldLogo));
            }

            $path = $request->file('logo')->store('logos', 'public');
            $url = Storage::url($path);
            $this->setSetting('logo_url', $url);

            return response()->json(['url' => $url]);
        }

        return response()->json(['message' => 'Logo yüklenemedi'], 400);
    }

    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|mimes:png,ico|max:512'
        ]);

        if ($request->hasFile('favicon')) {
            // Delete old favicon
            $oldFavicon = $this->getSetting('favicon_url');
            if ($oldFavicon) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $oldFavicon));
            }

            $path = $request->file('favicon')->store('favicons', 'public');
            $url = Storage::url($path);
            $this->setSetting('favicon_url', $url);

            return response()->json(['url' => $url]);
        }

        return response()->json(['message' => 'Favicon yüklenemedi'], 400);
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            Mail::raw('Bu bir test email\'idir.', function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Test Email - SportOnline');
            });

            return response()->json(['message' => 'Test email gönderildi']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Email gönderilemedi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getSetting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    private function setSetting($key, $value)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
