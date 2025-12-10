<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Vendor;
use App\Models\AccessLog;

class TokenController extends Controller
{
    /**
     * Tekil token yenileme
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|integer',
            'token' => 'required|string',
        ]);

        $vendor = Vendor::where('id', $request->vendor_id)
            ->where('is_active', true)
            ->first();

        if (!$vendor || $vendor->token !== $request->token || Carbon::parse($vendor->token_expiry)->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Geçersiz veya süresi dolmuş token'
            ], 403);
        }

        $newToken = Str::random(60);
        $newExpiry = now()->addDays(30);

        $vendor->update([
            'token' => $newToken,
            'token_expiry' => $newExpiry,
        ]);

        AccessLog::create([
            'vendor_id' => $vendor->id,
            'action' => 'token_refresh',
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'token' => $newToken,
                'token_expiry' => $newExpiry->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Toplu token yenileme
     */
    public function bulkRefresh(Request $request)
    {
        $request->validate([
            'vendor_ids' => 'required|array',
            'vendor_ids.*' => 'integer',
        ]);

        $vendors = Vendor::whereIn('id', $request->vendor_ids)
            ->where('is_active', true)
            ->get();

        $results = [];

        foreach ($vendors as $vendor) {
            $newToken = Str::random(60);
            $newExpiry = now()->addDays(30);

            $vendor->update([
                'token' => $newToken,
                'token_expiry' => $newExpiry,
            ]);

            AccessLog::create([
                'vendor_id' => $vendor->id,
                'action' => 'token_bulk_refresh',
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);

            $results[] = [
                'vendor_id' => $vendor->id,
                'token' => $newToken,
                'token_expiry' => $newExpiry->toDateTimeString(),
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $results,
        ]);
    }
}