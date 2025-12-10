<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HotelController extends Controller
{
    /**
     * Tüm otelleri listele
     */
    public function index(Request $request)
    {
        $query = Hotel::with(['category'])
            ->withCount(['rooms', 'reservations']);

        // Filtreler
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('stars')) {
            $query->where('stars', $request->stars);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('address', 'like', "%{$request->search}%");
            });
        }

        $hotels = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($hotels);
    }

    /**
     * İstatistikler
     */
    public function stats()
    {
        $stats = [
            'total_hotels' => Hotel::count(),
            'active_hotels' => Hotel::where('status', 'active')->count(),
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'total_reservations' => Reservation::count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'confirmed_reservations' => Reservation::where('status', 'confirmed')->count(),
            'revenue_today' => Reservation::whereDate('created_at', today())
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
            'revenue_month' => Reservation::whereMonth('created_at', now()->month)
                ->where('status', '!=', 'cancelled')
                ->sum('total_price'),
            'avg_rating' => Hotel::avg('rating') ?? 0,
            'occupancy_rate' => $this->calculateOccupancyRate(),
            'popular_cities' => Hotel::select('city', DB::raw('count(*) as count'))
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Doluluk oranı hesapla
     */
    private function calculateOccupancyRate()
    {
        $totalRooms = Room::where('status', '!=', 'maintenance')->count();
        if ($totalRooms === 0) return 0;

        $occupiedToday = Reservation::where('status', 'confirmed')
            ->whereDate('check_in', '<=', today())
            ->whereDate('check_out', '>=', today())
            ->count();

        return round(($occupiedToday / $totalRooms) * 100, 2);
    }

    /**
     * Otel detayı
     */
    public function show($id)
    {
        $hotel = Hotel::with([
            'category',
            'rooms.roomType',
            'reservations' => function ($q) {
                $q->latest()->limit(10);
            },
            'reviews' => function ($q) {
                $q->latest()->limit(5);
            },
            'amenities'
        ])->findOrFail($id);

        return response()->json($hotel);
    }

    /**
     * Yeni otel ekle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'address' => 'required|string',
            'city' => 'required|string',
            'district' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'stars' => 'nullable|integer|min:1|max:5',
            'check_in_time' => 'nullable|string',
            'check_out_time' => 'nullable|string',
            'policies' => 'nullable|array',
            'amenities' => 'nullable|array',
            'status' => 'in:active,inactive,pending',
            'is_featured' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|max:4096',
        ]);

        // Görselleri yükle
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('hotels', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['rating'] = 0;
        $validated['review_count'] = 0;

        $hotel = Hotel::create($validated);

        // Amenities bağla
        if (!empty($validated['amenities'])) {
            $hotel->amenities()->sync($validated['amenities']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Otel başarıyla eklendi',
            'hotel' => $hotel->load('amenities')
        ], 201);
    }

    /**
     * Otel güncelle
     */
    public function update(Request $request, $id)
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'district' => 'nullable|string',
            'country' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'phone' => 'sometimes|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'stars' => 'nullable|integer|min:1|max:5',
            'check_in_time' => 'nullable|string',
            'check_out_time' => 'nullable|string',
            'policies' => 'nullable|array',
            'amenities' => 'nullable|array',
            'status' => 'in:active,inactive,pending',
            'is_featured' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|max:4096',
        ]);

        // Görselleri yükle
        if ($request->hasFile('images')) {
            // Eski görselleri sil
            if ($hotel->images) {
                foreach ($hotel->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('hotels', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $hotel->update($validated);

        // Amenities güncelle
        if (isset($validated['amenities'])) {
            $hotel->amenities()->sync($validated['amenities']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Otel güncellendi',
            'hotel' => $hotel->fresh()->load('amenities')
        ]);
    }

    /**
     * Otel sil
     */
    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);

        // Aktif rezervasyon kontrolü
        if ($hotel->reservations()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            return response()->json([
                'error' => 'Aktif rezervasyonlar var, otel silinemez'
            ], 400);
        }

        // Görselleri sil
        if ($hotel->images) {
            foreach ($hotel->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $hotel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Otel silindi'
        ]);
    }

    // ==========================================
    // ODA YÖNETİMİ
    // ==========================================

    /**
     * Otel odalarını listele
     */
    public function getRooms($hotelId)
    {
        $rooms = Room::where('hotel_id', $hotelId)
            ->with('roomType')
            ->orderBy('room_number')
            ->get();

        return response()->json($rooms);
    }

    /**
     * Oda ekle
     */
    public function addRoom(Request $request, $hotelId)
    {
        $hotel = Hotel::findOrFail($hotelId);

        $validated = $request->validate([
            'room_number' => 'required|string|max:50',
            'room_type_id' => 'required|exists:room_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'floor' => 'nullable|integer',
            'capacity' => 'required|integer|min:1',
            'bed_type' => 'nullable|string',
            'size' => 'nullable|numeric',
            'price_per_night' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'status' => 'in:available,occupied,maintenance,reserved',
        ]);

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('rooms', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $validated['hotel_id'] = $hotelId;
        $room = Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Oda eklendi',
            'room' => $room
        ], 201);
    }

    /**
     * Oda güncelle
     */
    public function updateRoom(Request $request, $hotelId, $roomId)
    {
        $room = Room::where('hotel_id', $hotelId)->findOrFail($roomId);

        $validated = $request->validate([
            'room_number' => 'sometimes|string|max:50',
            'room_type_id' => 'sometimes|exists:room_types,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'floor' => 'nullable|integer',
            'capacity' => 'sometimes|integer|min:1',
            'bed_type' => 'nullable|string',
            'size' => 'nullable|numeric',
            'price_per_night' => 'sometimes|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'status' => 'in:available,occupied,maintenance,reserved',
        ]);

        if ($request->hasFile('images')) {
            if ($room->images) {
                foreach ($room->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('rooms', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Oda güncellendi',
            'room' => $room->fresh()
        ]);
    }

    /**
     * Oda sil
     */
    public function deleteRoom($hotelId, $roomId)
    {
        $room = Room::where('hotel_id', $hotelId)->findOrFail($roomId);

        // Aktif rezervasyon kontrolü
        if ($room->reservations()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            return response()->json([
                'error' => 'Bu odada aktif rezervasyon var'
            ], 400);
        }

        if ($room->images) {
            foreach ($room->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Oda silindi'
        ]);
    }

    // ==========================================
    // REZERVASYON YÖNETİMİ
    // ==========================================

    /**
     * Rezervasyonları listele
     */
    public function getReservations(Request $request, $hotelId = null)
    {
        $query = Reservation::with(['user', 'hotel', 'room']);

        if ($hotelId) {
            $query->where('hotel_id', $hotelId);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('check_in', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('check_out', '<=', $request->date_to);
        }

        $reservations = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($reservations);
    }

    /**
     * Rezervasyon detayı
     */
    public function getReservation($id)
    {
        $reservation = Reservation::with(['user', 'hotel', 'room', 'payments'])
            ->findOrFail($id);

        return response()->json($reservation);
    }

    /**
     * Yeni rezervasyon (Admin)
     */
    public function createReservation(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_id' => 'required|exists:rooms,id',
            'user_id' => 'nullable|exists:users,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'guest_phone' => 'required|string|max:20',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string',
            'status' => 'in:pending,confirmed,cancelled,completed',
        ]);

        // Oda müsaitlik kontrolü
        $room = Room::findOrFail($validated['room_id']);
        $isAvailable = !Reservation::where('room_id', $validated['room_id'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($validated) {
                $q->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                  ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']]);
            })->exists();

        if (!$isAvailable) {
            return response()->json(['error' => 'Bu tarihler için oda müsait değil'], 400);
        }

        // Toplam fiyat hesapla
        $nights = \Carbon\Carbon::parse($validated['check_in'])
            ->diffInDays(\Carbon\Carbon::parse($validated['check_out']));
        $validated['total_price'] = $nights * $room->price_per_night;
        $validated['nights'] = $nights;
        $validated['confirmation_code'] = strtoupper(Str::random(8));

        $reservation = Reservation::create($validated);

        // Odayı reserved olarak işaretle
        $room->update(['status' => 'reserved']);

        return response()->json([
            'success' => true,
            'message' => 'Rezervasyon oluşturuldu',
            'reservation' => $reservation
        ], 201);
    }

    /**
     * Rezervasyon durumu güncelle
     */
    public function updateReservationStatus(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
            'note' => 'nullable|string',
        ]);

        $oldStatus = $reservation->status;
        $reservation->update($validated);

        // Oda durumunu güncelle
        if ($validated['status'] === 'cancelled' || $validated['status'] === 'checked_out') {
            $reservation->room->update(['status' => 'available']);
        } elseif ($validated['status'] === 'checked_in') {
            $reservation->room->update(['status' => 'occupied']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Rezervasyon durumu güncellendi',
            'reservation' => $reservation->fresh()
        ]);
    }

    /**
     * Müsaitlik takvimi
     */
    public function getAvailabilityCalendar(Request $request, $hotelId)
    {
        $startDate = $request->get('start', now()->format('Y-m-d'));
        $endDate = $request->get('end', now()->addMonth()->format('Y-m-d'));

        $rooms = Room::where('hotel_id', $hotelId)
            ->with(['reservations' => function ($q) use ($startDate, $endDate) {
                $q->where('status', '!=', 'cancelled')
                  ->where(function ($query) use ($startDate, $endDate) {
                      $query->whereBetween('check_in', [$startDate, $endDate])
                            ->orWhereBetween('check_out', [$startDate, $endDate]);
                  });
            }])
            ->get();

        return response()->json($rooms);
    }

    // ============================================
    // PUBLIC API METHODS (No Authentication)
    // ============================================

    /**
     * Public: Aktif otelleri listele
     */
    public function publicIndex(Request $request)
    {
        $query = Hotel::where('status', 'active')
            ->withCount('rooms');

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('stars')) {
            $query->where('stars', $request->stars);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $hotels = $query->orderByDesc('rating')
            ->paginate($request->get('per_page', 20));

        return response()->json($hotels);
    }

    /**
     * Public: Otel detayı (slug ile)
     */
    public function publicShow($slug)
    {
        $hotel = Hotel::where('slug', $slug)
            ->where('status', 'active')
            ->with(['rooms' => function($q) {
                $q->where('status', 'available');
            }])
            ->firstOrFail();

        return response()->json($hotel);
    }

    /**
     * Public: Otel odaları
     */
    public function publicRooms($hotelId)
    {
        $hotel = Hotel::where('status', 'active')->findOrFail($hotelId);

        $rooms = Room::where('hotel_id', $hotel->id)
            ->where('status', '!=', 'maintenance')
            ->orderBy('price')
            ->get();

        return response()->json([
            'hotel' => $hotel->only(['id', 'name', 'slug']),
            'rooms' => $rooms
        ]);
    }

    /**
     * Public: Müsaitlik kontrolü
     */
    public function checkAvailability(Request $request, $hotelId)
    {
        $validated = $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'room_type' => 'nullable|string',
            'guests' => 'nullable|integer|min:1'
        ]);

        $hotel = Hotel::where('status', 'active')->findOrFail($hotelId);

        $query = Room::where('hotel_id', $hotelId)
            ->where('status', 'available');

        if (!empty($validated['room_type'])) {
            $query->where('type', $validated['room_type']);
        }

        if (!empty($validated['guests'])) {
            $query->where('capacity', '>=', $validated['guests']);
        }

        // Tarihlerde rezervasyonu olmayan odaları bul
        $bookedRoomIds = Reservation::where('hotel_id', $hotelId)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                  ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('check_in', '<=', $validated['check_in'])
                         ->where('check_out', '>=', $validated['check_out']);
                  });
            })
            ->pluck('room_id');

        $availableRooms = $query->whereNotIn('id', $bookedRoomIds)->get();

        return response()->json([
            'available' => $availableRooms->count() > 0,
            'rooms' => $availableRooms,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out']
        ]);
    }
}
