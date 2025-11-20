<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\SellerSettings;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SellerSettingsController extends Controller
{
    /**
     * Get all seller settings
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();
        
        if (!$vendor) {
            return response()->json(['error' => 'Satıcı bulunamadı'], 404);
        }

        $settings = SellerSettings::where('vendor_id', $vendor->id)->first();
        $apiKeys = ApiKey::where('vendor_id', $vendor->id)->get();

        return response()->json([
            'profile' => [
                'store_name' => $vendor->name ?? '',
                'description' => $vendor->description ?? '',
                'logo_url' => $vendor->logo_url ?? '',
                'cover_url' => $vendor->cover_url ?? '',
                'contact_email' => $vendor->email ?? '',
                'contact_phone' => $vendor->phone ?? '',
                'address' => $vendor->address ?? '',
                'city' => $vendor->city ?? '',
                'postal_code' => $vendor->postal_code ?? '',
            ],
            'payment' => $settings ? [
                'bank_name' => $settings->bank_name,
                'account_holder' => $settings->account_holder,
                'iban' => $settings->iban,
                'branch_code' => $settings->branch_code,
            ] : null,
            'shipping' => $settings ? [
                'carriers' => json_decode($settings->shipping_carriers ?? '[]'),
                'zones' => json_decode($settings->shipping_zones ?? '[]'),
            ] : null,
            'notifications' => $settings ? [
                'email' => json_decode($settings->email_notifications ?? '{}'),
                'sms' => json_decode($settings->sms_notifications ?? '{}'),
                'push' => json_decode($settings->push_notifications ?? '{}'),
            ] : null,
            'tax' => $settings ? [
                'company_type' => $settings->company_type,
                'tax_office' => $settings->tax_office,
                'tax_number' => $settings->tax_number,
                'trade_registry_number' => $settings->trade_registry_number,
                'company_title' => $settings->company_title,
            ] : null,
            'api_keys' => $apiKeys->map(function ($key) {
                return [
                    'id' => $key->id,
                    'name' => $key->name,
                    'key' => $key->key,
                    'created_at' => $key->created_at,
                ];
            }),
        ]);
    }

    /**
     * Update store profile
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'cover_url' => 'nullable|url',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);

        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return response()->json(['error' => 'Satıcı bulunamadı'], 404);
        }

        $vendor->update([
            'name' => $validated['store_name'],
            'description' => $validated['description'],
            'logo_url' => $validated['logo_url'],
            'cover_url' => $validated['cover_url'],
            'email' => $validated['contact_email'],
            'phone' => $validated['contact_phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'postal_code' => $validated['postal_code'],
        ]);

        return response()->json(['message' => 'Profil güncellendi', 'vendor' => $vendor]);
    }

    /**
     * Update payment information
     */
    public function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string',
            'account_holder' => 'required|string',
            'iban' => 'required|string|max:32',
            'branch_code' => 'nullable|string',
        ]);

        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return response()->json(['error' => 'Satıcı bulunamadı'], 404);
        }

        $settings = SellerSettings::updateOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'bank_name' => $validated['bank_name'],
                'account_holder' => $validated['account_holder'],
                'iban' => $validated['iban'],
                'branch_code' => $validated['branch_code'],
            ]
        );

        return response()->json(['message' => 'Ödeme bilgileri güncellendi', 'settings' => $settings]);
    }

    /**
     * Update shipping settings
     */
    public function updateShipping(Request $request)
    {
        $validated = $request->validate([
            'carriers' => 'nullable|array',
            'zones' => 'nullable|array',
            'zones.*.name' => 'required|string',
            'zones.*.price' => 'required|numeric|min:0',
            'zones.*.free_shipping_threshold' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return response()->json(['error' => 'Satıcı bulunamadı'], 404);
        }

        $settings = SellerSettings::updateOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'shipping_carriers' => json_encode($validated['carriers'] ?? []),
                'shipping_zones' => json_encode($validated['zones'] ?? []),
            ]
        );

        return response()->json(['message' => 'Kargo ayarları güncellendi', 'settings' => $settings]);
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email' => 'nullable|array',
            'sms' => 'nullable|array',
            'push' => 'nullable|array',
        ]);

        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return response()->json(['error' => 'Satıcı bulunamadı'], 404);
        }

        $settings = SellerSettings::updateOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'email_notifications' => json_encode($validated['email'] ?? []),
                'sms_notifications' => json_encode($validated['sms'] ?? []),
                'push_notifications' => json_encode($validated['push'] ?? []),
            ]
        );

        return response()->json(['message' => 'Bildirim tercihleri güncellendi', 'settings' => $settings]);
    }

    /**
     * Update tax settings
     */
    public function updateTax(Request $request)
    {
        $validated = $request->validate([
            'company_type' => 'required|in:individual,limited,joint_stock',
            'tax_office' => 'required|string',
            'tax_number' => 'required|string|max:11',
            'trade_registry_number' => 'nullable|string',
            'company_title' => 'required|string',
        ]);

        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return response()->json(['error' => 'Satıcı bulunamadı'], 404);
        }

        $settings = SellerSettings::updateOrCreate(
            ['vendor_id' => $vendor->id],
            [
                'company_type' => $validated['company_type'],
                'tax_office' => $validated['tax_office'],
                'tax_number' => $validated['tax_number'],
                'trade_registry_number' => $validated['trade_registry_number'],
                'company_title' => $validated['company_title'],
            ]
        );

        return response()->json(['message' => 'Vergi bilgileri güncellendi', 'settings' => $settings]);
    }

    /**
     * Create new API key
     */
    public function createApiKey(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return response()->json(['error' => 'Satıcı bulunamadı'], 404);
        }

        $apiKey = ApiKey::create([
            'vendor_id' => $vendor->id,
            'name' => $validated['name'],
            'key' => 'sk_' . Str::random(48),
        ]);

        return response()->json([
            'message' => 'API anahtarı oluşturuldu',
            'api_key' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'key' => $apiKey->key,
                'created_at' => $apiKey->created_at,
            ]
        ]);
    }

    /**
     * Delete API key
     */
    public function deleteApiKey($id)
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return response()->json(['error' => 'Satıcı bulunamadı'], 404);
        }

        $apiKey = ApiKey::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$apiKey) {
            return response()->json(['error' => 'API anahtarı bulunamadı'], 404);
        }

        $apiKey->delete();

        return response()->json(['message' => 'API anahtarı silindi']);
    }
}
