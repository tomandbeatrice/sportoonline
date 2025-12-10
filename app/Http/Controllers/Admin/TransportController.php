<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Transfer;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransportController extends Controller
{
    // ==========================================
    // ARAÇ YÖNETİMİ
    // ==========================================

    /**
     * Araçları listele
     */
    public function getVehicles(Request $request)
    {
        $query = Vehicle::with(['driver', 'vehicleType']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('vehicle_type_id', $request->type);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('plate_number', 'like', "%{$request->search}%")
                  ->orWhere('brand', 'like', "%{$request->search}%")
                  ->orWhere('model', 'like', "%{$request->search}%");
            });
        }

        $vehicles = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($vehicles);
    }

    /**
     * İstatistikler
     */
    public function stats()
    {
        $stats = [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::where('status', 'active')->count(),
            'total_drivers' => Driver::count(),
            'available_drivers' => Driver::where('status', 'available')->count(),
            'total_transfers' => Transfer::count(),
            'pending_transfers' => Transfer::where('status', 'pending')->count(),
            'active_transfers' => Transfer::whereIn('status', ['confirmed', 'in_progress'])->count(),
            'completed_today' => Transfer::whereDate('scheduled_at', today())
                ->where('status', 'completed')
                ->count(),
            'revenue_today' => Transfer::whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('price'),
            'revenue_month' => Transfer::whereMonth('created_at', now()->month)
                ->where('status', 'completed')
                ->sum('price'),
            'avg_rating' => Driver::avg('rating') ?? 0,
            'vehicle_types' => DB::table('vehicles')
                ->join('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id')
                ->select('vehicle_types.name', DB::raw('count(*) as count'))
                ->groupBy('vehicle_types.name')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Araç ekle
     */
    public function storeVehicle(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number|max:20',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1|max:50',
            'luggage_capacity' => 'nullable|integer|min:0',
            'driver_id' => 'nullable|exists:drivers,id',
            'features' => 'nullable|array',
            'insurance_expiry' => 'nullable|date',
            'inspection_expiry' => 'nullable|date',
            'status' => 'in:active,inactive,maintenance',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('vehicles', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $vehicle = Vehicle::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Araç eklendi',
            'vehicle' => $vehicle
        ], 201);
    }

    /**
     * Araç güncelle
     */
    public function updateVehicle(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'plate_number' => 'sometimes|string|max:20|unique:vehicles,plate_number,' . $id,
            'vehicle_type_id' => 'sometimes|exists:vehicle_types,id',
            'brand' => 'sometimes|string|max:100',
            'model' => 'sometimes|string|max:100',
            'year' => 'sometimes|integer|min:2000|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:50',
            'capacity' => 'sometimes|integer|min:1|max:50',
            'luggage_capacity' => 'nullable|integer|min:0',
            'driver_id' => 'nullable|exists:drivers,id',
            'features' => 'nullable|array',
            'insurance_expiry' => 'nullable|date',
            'inspection_expiry' => 'nullable|date',
            'status' => 'in:active,inactive,maintenance',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        if ($request->hasFile('images')) {
            if ($vehicle->images) {
                foreach ($vehicle->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('vehicles', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $vehicle->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Araç güncellendi',
            'vehicle' => $vehicle->fresh()
        ]);
    }

    /**
     * Araç sil
     */
    public function deleteVehicle($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        if ($vehicle->transfers()->whereIn('status', ['pending', 'confirmed', 'in_progress'])->exists()) {
            return response()->json([
                'error' => 'Bu araca atanmış aktif transferler var'
            ], 400);
        }

        if ($vehicle->images) {
            foreach ($vehicle->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Araç silindi'
        ]);
    }

    // ==========================================
    // SÜRÜCÜ YÖNETİMİ
    // ==========================================

    /**
     * Sürücüleri listele
     */
    public function getDrivers(Request $request)
    {
        $query = Driver::with(['user', 'vehicle']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $drivers = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($drivers);
    }

    /**
     * Sürücü ekle
     */
    public function storeDriver(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:drivers,user_id',
            'license_number' => 'required|string|unique:drivers,license_number|max:50',
            'license_expiry' => 'required|date|after:today',
            'license_type' => 'required|string|max:20',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0',
            'languages' => 'nullable|array',
            'status' => 'in:available,on_trip,offline,suspended',
            'photo' => 'nullable|image|max:2048',
            'documents' => 'nullable|array',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('drivers', 'public');
        }

        $validated['rating'] = 5.0;
        $validated['total_trips'] = 0;

        $driver = Driver::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sürücü eklendi',
            'driver' => $driver->load('user')
        ], 201);
    }

    /**
     * Sürücü güncelle
     */
    public function updateDriver(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validated = $request->validate([
            'license_number' => 'sometimes|string|max:50|unique:drivers,license_number,' . $id,
            'license_expiry' => 'sometimes|date',
            'license_type' => 'sometimes|string|max:20',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'phone' => 'sometimes|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0',
            'languages' => 'nullable|array',
            'status' => 'in:available,on_trip,offline,suspended',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($driver->photo) {
                Storage::disk('public')->delete($driver->photo);
            }
            $validated['photo'] = $request->file('photo')->store('drivers', 'public');
        }

        $driver->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sürücü güncellendi',
            'driver' => $driver->fresh()->load('user')
        ]);
    }

    /**
     * Sürücü sil
     */
    public function deleteDriver($id)
    {
        $driver = Driver::findOrFail($id);

        if ($driver->transfers()->whereIn('status', ['pending', 'confirmed', 'in_progress'])->exists()) {
            return response()->json([
                'error' => 'Bu sürücüye atanmış aktif transferler var'
            ], 400);
        }

        if ($driver->photo) {
            Storage::disk('public')->delete($driver->photo);
        }

        $driver->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sürücü silindi'
        ]);
    }

    // ==========================================
    // TRANSFER YÖNETİMİ
    // ==========================================

    /**
     * Transferleri listele
     */
    public function getTransfers(Request $request)
    {
        $query = Transfer::with(['user', 'driver.user', 'vehicle']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $transfers = $query->orderByDesc('scheduled_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($transfers);
    }

    /**
     * Transfer detayı
     */
    public function getTransfer($id)
    {
        $transfer = Transfer::with(['user', 'driver.user', 'vehicle', 'route'])
            ->findOrFail($id);

        return response()->json($transfer);
    }

    /**
     * Transfer oluştur (Admin)
     */
    public function storeTransfer(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:20',
            'passenger_email' => 'nullable|email',
            'pickup_address' => 'required|string',
            'pickup_latitude' => 'nullable|numeric',
            'pickup_longitude' => 'nullable|numeric',
            'dropoff_address' => 'required|string',
            'dropoff_latitude' => 'nullable|numeric',
            'dropoff_longitude' => 'nullable|numeric',
            'scheduled_at' => 'required|date|after:now',
            'passengers' => 'required|integer|min:1',
            'luggage' => 'nullable|integer|min:0',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'price' => 'required|numeric|min:0',
            'flight_number' => 'nullable|string|max:20',
            'special_requests' => 'nullable|string',
            'status' => 'in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $validated['booking_code'] = strtoupper(Str::random(8));

        $transfer = Transfer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transfer oluşturuldu',
            'transfer' => $transfer
        ], 201);
    }

    /**
     * Transfer güncelle
     */
    public function updateTransfer(Request $request, $id)
    {
        $transfer = Transfer::findOrFail($id);

        $validated = $request->validate([
            'passenger_name' => 'sometimes|string|max:255',
            'passenger_phone' => 'sometimes|string|max:20',
            'pickup_address' => 'sometimes|string',
            'dropoff_address' => 'sometimes|string',
            'scheduled_at' => 'sometimes|date',
            'passengers' => 'sometimes|integer|min:1',
            'luggage' => 'nullable|integer|min:0',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'price' => 'sometimes|numeric|min:0',
            'special_requests' => 'nullable|string',
            'status' => 'in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $transfer->update($validated);

        // Sürücü durumunu güncelle
        if (isset($validated['status'])) {
            if ($validated['status'] === 'in_progress' && $transfer->driver) {
                $transfer->driver->update(['status' => 'on_trip']);
            } elseif (in_array($validated['status'], ['completed', 'cancelled']) && $transfer->driver) {
                $transfer->driver->update(['status' => 'available']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Transfer güncellendi',
            'transfer' => $transfer->fresh()
        ]);
    }

    /**
     * Transfer sürücü ata
     */
    public function assignDriver(Request $request, $id)
    {
        $transfer = Transfer::findOrFail($id);

        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        $driver = Driver::findOrFail($validated['driver_id']);

        if ($driver->status !== 'available') {
            return response()->json([
                'error' => 'Sürücü müsait değil'
            ], 400);
        }

        $transfer->update([
            'driver_id' => $validated['driver_id'],
            'vehicle_id' => $validated['vehicle_id'] ?? $driver->vehicle_id,
            'status' => 'confirmed'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sürücü atandı',
            'transfer' => $transfer->fresh()->load(['driver.user', 'vehicle'])
        ]);
    }

    // ==========================================
    // ROTA YÖNETİMİ
    // ==========================================

    /**
     * Rotaları listele
     */
    public function getRoutes(Request $request)
    {
        $query = Route::query();

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('origin', 'like', "%{$request->search}%")
                  ->orWhere('destination', 'like', "%{$request->search}%");
            });
        }

        $routes = $query->orderBy('origin')
            ->paginate($request->get('per_page', 20));

        return response()->json($routes);
    }

    /**
     * Rota ekle
     */
    public function storeRoute(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|string|max:255',
            'origin_type' => 'required|in:airport,hotel,address,city_center',
            'destination' => 'required|string|max:255',
            'destination_type' => 'required|in:airport,hotel,address,city_center',
            'distance_km' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'base_price' => 'required|numeric|min:0',
            'price_per_km' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $route = Route::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rota eklendi',
            'route' => $route
        ], 201);
    }

    /**
     * Rota güncelle
     */
    public function updateRoute(Request $request, $id)
    {
        $route = Route::findOrFail($id);

        $validated = $request->validate([
            'origin' => 'sometimes|string|max:255',
            'origin_type' => 'sometimes|in:airport,hotel,address,city_center',
            'destination' => 'sometimes|string|max:255',
            'destination_type' => 'sometimes|in:airport,hotel,address,city_center',
            'distance_km' => 'sometimes|numeric|min:0',
            'duration_minutes' => 'sometimes|integer|min:0',
            'base_price' => 'sometimes|numeric|min:0',
            'price_per_km' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $route->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rota güncellendi',
            'route' => $route->fresh()
        ]);
    }

    /**
     * Rota sil
     */
    public function deleteRoute($id)
    {
        $route = Route::findOrFail($id);
        $route->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rota silindi'
        ]);
    }

    // ============================================
    // PUBLIC API METHODS (No Authentication)
    // ============================================

    /**
     * Public: Aktif rotaları listele
     */
    public function publicRoutes(Request $request)
    {
        $query = Route::where('is_active', true);

        if ($request->has('origin')) {
            $query->where('origin', 'like', "%{$request->origin}%");
        }

        if ($request->has('destination')) {
            $query->where('destination', 'like', "%{$request->destination}%");
        }

        if ($request->has('origin_type')) {
            $query->where('origin_type', $request->origin_type);
        }

        $routes = $query->orderBy('origin')
            ->paginate($request->get('per_page', 50));

        return response()->json($routes);
    }

    /**
     * Public: Rota detayı (slug ile)
     */
    public function publicShowRoute($slug)
    {
        $route = Route::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json($route);
    }

    /**
     * Public: Fiyat hesapla
     */
    public function calculatePrice(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'nullable|exists:routes,id',
            'origin' => 'required_without:route_id|string',
            'destination' => 'required_without:route_id|string',
            'passengers' => 'required|integer|min:1|max:50',
            'vehicle_type' => 'nullable|string',
            'date' => 'nullable|date|after_or_equal:today',
            'return_trip' => 'boolean'
        ]);

        // Eğer route_id varsa direkt fiyat al
        if (!empty($validated['route_id'])) {
            $route = Route::findOrFail($validated['route_id']);
            $basePrice = $route->base_price;
            $distance = $route->distance_km;
        } else {
            // Rota bul veya tahmini hesapla
            $route = Route::where('origin', 'like', "%{$validated['origin']}%")
                ->where('destination', 'like', "%{$validated['destination']}%")
                ->where('is_active', true)
                ->first();

            if ($route) {
                $basePrice = $route->base_price;
                $distance = $route->distance_km;
            } else {
                // Tahmini fiyat (km başına 2 TL varsayılan)
                $basePrice = 100; // Minimum fiyat
                $distance = 0;
            }
        }

        // Yolcu sayısına göre ayarlama (4'ten fazla için +%20)
        $passengerMultiplier = $validated['passengers'] > 4 ? 1.2 : 1;

        // Gece tarifesi kontrolü (22:00 - 06:00 arası +%30)
        $nightMultiplier = 1;
        if (!empty($validated['date'])) {
            $hour = date('H', strtotime($validated['date']));
            if ($hour >= 22 || $hour < 6) {
                $nightMultiplier = 1.3;
            }
        }

        // Gidiş-dönüş indirimi
        $returnMultiplier = !empty($validated['return_trip']) ? 1.8 : 1;

        $totalPrice = $basePrice * $passengerMultiplier * $nightMultiplier * $returnMultiplier;

        return response()->json([
            'base_price' => $basePrice,
            'distance_km' => $distance,
            'passengers' => $validated['passengers'],
            'passenger_multiplier' => $passengerMultiplier,
            'night_multiplier' => $nightMultiplier,
            'return_trip' => $validated['return_trip'] ?? false,
            'return_multiplier' => $returnMultiplier,
            'total_price' => round($totalPrice, 2),
            'currency' => 'TRY'
        ]);
    }
}
