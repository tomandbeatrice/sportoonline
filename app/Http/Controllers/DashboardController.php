<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderLog;

class DashboardController extends Controller
{
    /**
     * Cockpit ekranı için sipariş loglarını getirir.
     * Arama ve sipariş durumu filtresi desteklenir.
     */
    public function getModuleLogs(Request $request)
    {
        $query = OrderLog::query()->with('order');

        // 🔍 Arama filtresi: mesaj içeriği veya sipariş ID
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('order_id', $search);
            });
        }

        // 📦 Sipariş durumu filtresi
        if ($request->filled('status')) {
            $query->whereHas('order', fn($q) => $q->where('status', $request->status));
        }

        // 🔄 Son 100 logu getir
        return $query->latest()->take(100)->get();
    }
}