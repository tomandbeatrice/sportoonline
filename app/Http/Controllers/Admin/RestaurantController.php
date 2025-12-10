<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\FoodOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    /**
     * Tüm restoranları listele
     */
    public function index(Request $request)
    {
        $query = Restaurant::with(['category', 'owner'])
            ->withCount(['menuItems', 'orders']);

        // Filtreler
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('address', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        $restaurants = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($restaurants);
    }

    /**
     * İstatistikler
     */
    public function stats()
    {
        $stats = [
            'total_restaurants' => Restaurant::count(),
            'active_restaurants' => Restaurant::where('status', 'active')->count(),
            'pending_restaurants' => Restaurant::where('status', 'pending')->count(),
            'total_menu_items' => MenuItem::count(),
            'total_orders' => FoodOrder::count(),
            'todays_orders' => FoodOrder::whereDate('created_at', today())->count(),
            'revenue_today' => FoodOrder::whereDate('created_at', today())->sum('total'),
            'revenue_month' => FoodOrder::whereMonth('created_at', now()->month)->sum('total'),
            'avg_rating' => Restaurant::avg('rating') ?? 0,
            'top_categories' => DB::table('restaurants')
                ->join('categories', 'restaurants.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('count(*) as count'))
                ->groupBy('categories.name')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Restoran detayı
     */
    public function show($id)
    {
        $restaurant = Restaurant::with([
            'category',
            'owner',
            'menuItems.category',
            'orders' => function ($q) {
                $q->latest()->limit(10);
            },
            'reviews' => function ($q) {
                $q->latest()->limit(5);
            }
        ])->findOrFail($id);

        return response()->json($restaurant);
    }

    /**
     * Yeni restoran ekle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'owner_id' => 'nullable|exists:users,id',
            'address' => 'required|string',
            'city' => 'required|string',
            'district' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'opening_hours' => 'nullable|array',
            'delivery_radius' => 'nullable|numeric',
            'min_order_amount' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'avg_delivery_time' => 'nullable|integer',
            'status' => 'in:active,inactive,pending',
            'is_featured' => 'boolean',
            'logo' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:4096',
        ]);

        // Logo yükleme
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('restaurants/logos', 'public');
        }

        // Kapak resmi yükleme
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('restaurants/covers', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['rating'] = 0;
        $validated['review_count'] = 0;

        $restaurant = Restaurant::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Restoran başarıyla eklendi',
            'restaurant' => $restaurant
        ], 201);
    }

    /**
     * Restoran güncelle
     */
    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'owner_id' => 'nullable|exists:users,id',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'district' => 'nullable|string',
            'phone' => 'sometimes|string|max:20',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'opening_hours' => 'nullable|array',
            'delivery_radius' => 'nullable|numeric',
            'min_order_amount' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'avg_delivery_time' => 'nullable|integer',
            'status' => 'in:active,inactive,pending',
            'is_featured' => 'boolean',
            'logo' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:4096',
        ]);

        // Logo yükleme
        if ($request->hasFile('logo')) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $validated['logo'] = $request->file('logo')->store('restaurants/logos', 'public');
        }

        // Kapak resmi yükleme
        if ($request->hasFile('cover_image')) {
            if ($restaurant->cover_image) {
                Storage::disk('public')->delete($restaurant->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('restaurants/covers', 'public');
        }

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $restaurant->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Restoran güncellendi',
            'restaurant' => $restaurant->fresh()
        ]);
    }

    /**
     * Restoran sil
     */
    public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        // İlişkili verileri kontrol et
        if ($restaurant->orders()->where('status', 'pending')->exists()) {
            return response()->json([
                'error' => 'Bekleyen siparişler var, restoran silinemez'
            ], 400);
        }

        // Görselleri sil
        if ($restaurant->logo) {
            Storage::disk('public')->delete($restaurant->logo);
        }
        if ($restaurant->cover_image) {
            Storage::disk('public')->delete($restaurant->cover_image);
        }

        $restaurant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Restoran silindi'
        ]);
    }

    /**
     * Restoran durumunu değiştir
     */
    public function toggleStatus($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $newStatus = $restaurant->status === 'active' ? 'inactive' : 'active';
        $restaurant->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Restoran durumu güncellendi',
            'status' => $newStatus
        ]);
    }

    /**
     * Öne çıkarma toggle
     */
    public function toggleFeatured($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->update(['is_featured' => !$restaurant->is_featured]);

        return response()->json([
            'success' => true,
            'message' => $restaurant->is_featured ? 'Restoran öne çıkarıldı' : 'Öne çıkarma kaldırıldı'
        ]);
    }

    // ==========================================
    // MENÜ YÖNETİMİ
    // ==========================================

    /**
     * Restoran menüsünü getir
     */
    public function getMenu($restaurantId)
    {
        $menuItems = MenuItem::where('restaurant_id', $restaurantId)
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category.name');

        return response()->json($menuItems);
    }

    /**
     * Menü öğesi ekle
     */
    public function addMenuItem(Request $request, $restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'preparation_time' => 'nullable|integer',
            'calories' => 'nullable|integer',
            'allergens' => 'nullable|array',
            'options' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $validated['restaurant_id'] = $restaurantId;
        $menuItem = MenuItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menü öğesi eklendi',
            'menu_item' => $menuItem
        ], 201);
    }

    /**
     * Menü öğesi güncelle
     */
    public function updateMenuItem(Request $request, $restaurantId, $itemId)
    {
        $menuItem = MenuItem::where('restaurant_id', $restaurantId)
            ->findOrFail($itemId);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'sometimes|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'preparation_time' => 'nullable|integer',
            'calories' => 'nullable|integer',
            'allergens' => 'nullable|array',
            'options' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menü öğesi güncellendi',
            'menu_item' => $menuItem->fresh()
        ]);
    }

    /**
     * Menü öğesi sil
     */
    public function deleteMenuItem($restaurantId, $itemId)
    {
        $menuItem = MenuItem::where('restaurant_id', $restaurantId)
            ->findOrFail($itemId);

        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }

        $menuItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menü öğesi silindi'
        ]);
    }

    // ==========================================
    // SİPARİŞ YÖNETİMİ
    // ==========================================

    /**
     * Restoran siparişlerini listele
     */
    public function getOrders(Request $request, $restaurantId = null)
    {
        $query = FoodOrder::with(['user', 'restaurant', 'items.menuItem']);

        if ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($orders);
    }

    /**
     * Sipariş durumu güncelle
     */
    public function updateOrderStatus(Request $request, $orderId)
    {
        $order = FoodOrder::findOrFail($orderId);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,delivered,cancelled',
            'note' => 'nullable|string|max:500',
            'estimated_delivery_time' => 'nullable|date'
        ]);

        $order->update($validated);

        // Bildirim gönder
        // event(new FoodOrderStatusChanged($order));

        return response()->json([
            'success' => true,
            'message' => 'Sipariş durumu güncellendi',
            'order' => $order->fresh()
        ]);
    }

    /**
     * Sipariş ata (kurye)
     */
    public function assignDriver(Request $request, $orderId)
    {
        $order = FoodOrder::findOrFail($orderId);

        $validated = $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $order->update([
            'driver_id' => $validated['driver_id'],
            'status' => 'out_for_delivery'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kurye atandı'
        ]);
    }

    // ============================================
    // PUBLIC API METHODS (No Authentication)
    // ============================================

    /**
     * Public: Aktif restoranları listele
     */
    public function publicIndex(Request $request)
    {
        $query = Restaurant::where('status', 'active')
            ->with(['category'])
            ->withCount('menuItems');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $restaurants = $query->orderByDesc('rating')
            ->paginate($request->get('per_page', 20));

        return response()->json($restaurants);
    }

    /**
     * Public: Restoran detayı (slug ile)
     */
    public function publicShow($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'active')
            ->with(['category', 'menuItems'])
            ->firstOrFail();

        return response()->json($restaurant);
    }

    /**
     * Public: Restoran menüsü
     */
    public function publicMenu($restaurantId)
    {
        $restaurant = Restaurant::where('status', 'active')->findOrFail($restaurantId);

        $menuItems = MenuItem::where('restaurant_id', $restaurant->id)
            ->where('is_available', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return response()->json([
            'restaurant' => $restaurant->only(['id', 'name', 'slug']),
            'menu' => $menuItems
        ]);
    }
}
