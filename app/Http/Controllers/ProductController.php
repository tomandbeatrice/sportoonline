<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Ürünleri filtreleyip listeler
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        // Text Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Price Range Filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Variant Filters (Color & Size)
        if ($request->filled('color') || $request->filled('size')) {
            $query->whereHas('variants', function ($q) use ($request) {
                if ($request->filled('color')) {
                    $q->whereIn('color', explode(',', $request->color));
                }
                if ($request->filled('size')) {
                    $q->whereIn('size', explode(',', $request->size));
                }
            });
        }

        // Brand/Vendor Filter
        if ($request->filled('brand')) {
            // Assuming brand is stored in vendors table and linked via vendor_id
            $query->whereHas('vendor', function ($q) use ($request) {
                $q->whereIn('name', explode(',', $request->brand));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                case 'popular':
                    // Assuming we have an order_items count or similar
                    $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        
        // Add active campaigns to each product
        $products->getCollection()->transform(function ($product) {
            $product->active_campaign = $this->getActiveCampaignForProduct($product);
            return $product;
        });
        
        // Aggregations for filters
        $categories = Category::select('id', 'name')->get();
        
        // Get available colors and sizes from variants for the sidebar
        $colors = \App\Models\Variant::distinct()->whereNotNull('color')->pluck('color');
        $sizes = \App\Models\Variant::distinct()->whereNotNull('size')->pluck('size');
        $brands = Vendor::distinct()->pluck('name');

        return response()->json([
            'products' => $products,
            'filters' => [
                'categories' => $categories,
                'colors' => $colors,
                'sizes' => $sizes,
                'brands' => $brands,
            ]
        ]);
    }

    /**
     * Search Autocomplete
     */
    public function autocomplete(Request $request)
    {
        $search = $request->get('query');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $products = Product::where('name', 'like', "%{$search}%")
            ->select('id', 'name', 'image_url', 'price')
            ->take(5)
            ->get();

        $categories = Category::where('name', 'like', "%{$search}%")
            ->select('id', 'name')
            ->take(3)
            ->get();

        return response()->json([
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * Satıcı için ürün oluştur.
     */
    public function store(Request $request)
    {
        $seller = $this->ensureSeller($request);
        $data = $request->validate($this->productRules());

        $vendor = $this->resolveVendor($seller);

        $product = Product::create(array_merge($data, [
            'seller_id' => $seller->id,
            'vendor_id' => $vendor->id,
            'is_active' => $data['is_active'] ?? true,
        ]));

        return response()->json($product->fresh('category'), 201);
    }

    // 🧪 Ürün detayı — Vue için JSON döner
    public function show(Product $product)
    {
        $product->load(['variants', 'category', 'comments', 'orderDetails', 'seller']);

        // Satıcının diğer ürünlerini al (mevcut ürün hariç, max 6 adet)
        $sellerOtherProducts = [];
        if ($product->seller) {
            $sellerOtherProducts = Product::where('seller_id', $product->seller_id)
                ->where('id', '!=', $product->id)
                ->take(6)
                ->get(['id', 'name', 'price', 'image']);
        }

        // Satıcı puanını hesapla (yorumlara göre)
        $sellerRating = 0;
        if ($product->seller) {
            $sellerProducts = Product::where('seller_id', $product->seller_id)->pluck('id');
            $sellerRating = \App\Models\Review::whereIn('product_id', $sellerProducts)
                ->avg('rating') ?? 0;
        }

        // Get active campaign for this product
        $activeCampaign = $this->getActiveCampaignForProduct($product);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'image' => $product->image,
            'category' => $product->category->name ?? null,
            'variants' => $product->variants,
            'comments_count' => $product->comments->count(),
            'order_count' => $product->orderDetails->sum('quantity'),
            'active_campaign' => $activeCampaign,
            'seller' => $product->seller ? [
                'id' => $product->seller->id,
                'name' => $product->seller->name,
                'email' => $product->seller->email,
                'rating' => round($sellerRating, 1),
                'other_products' => $sellerOtherProducts,
            ] : null,
        ]);
    }

    // 🧠 İstatistik endpoint’i — Vue dashboard için
    public function stats(Product $product)
    {
        $product->load(['orderDetails', 'comments']);

        return response()->json([
            'id' => $product->id,
            'total_orders' => $product->orderDetails->sum('quantity'),
            'total_comments' => $product->comments->count(),
            'total_revenue' => $product->orderDetails->sum(fn($detail) =>
                $detail->quantity * $detail->price
            ),
        ]);
    }

    /**
     * Satıcı ürünü günceller.
     */
    public function update(Request $request, Product $product)
    {
        $seller = $this->ensureSeller($request);
        $this->authorizeSellerProduct($seller, $product);

        $data = $request->validate($this->productRules(true));
        $product->fill($data);
        $product->save();

        return response()->json($product->fresh('category'));
    }

    /**
     * Satıcı ürünü siler.
     */
    public function destroy(Request $request, Product $product)
    {
        $seller = $this->ensureSeller($request);
        $this->authorizeSellerProduct($seller, $product);

        $product->delete();

        return response()->json(['message' => 'Ürün silindi']);
    }

    private function ensureSeller(Request $request): User
    {
        $user = $request->user();
        abort_if(!$user || !$user->isSeller(), 403, 'Bu işlem yalnızca satıcılar tarafından yapılabilir.');

        return $user;
    }

    private function resolveVendor(User $seller): Vendor
    {
        $vendor = Vendor::firstOrNew(['user_id' => $seller->id]);

        if (!$vendor->exists) {
            $vendor->name = $seller->name ?? 'Mağaza #' . $seller->id;
        }

        $vendor->save();

        return $vendor;
    }

    private function authorizeSellerProduct(User $seller, Product $product): void
    {
        abort_if($product->seller_id !== $seller->id, 403, 'Bu ürünü yönetme yetkiniz yok.');
    }

    private function productRules(bool $isUpdate = false): array
    {
        $presence = $isUpdate ? 'sometimes' : 'required';

        return [
            'name' => [$presence, 'string', 'max:255'],
            'description' => [$presence, 'string'],
            'price' => [$presence, 'numeric', 'min:0'],
            'stock' => [$presence, 'integer', 'min:0'],
            'category_id' => [$presence, 'exists:categories,id'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'sku' => ['nullable', 'string', 'max:100'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, Product $product)
    {
        $seller = $this->ensureSeller($request);
        $this->authorizeSellerProduct($seller, $product);

        $data = $request->validate([
            'type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $product->updateStock(
            $data['type'],
            $data['quantity'],
            $data['note'] ?? null,
            'manual'
        );

        return response()->json([
            'success' => true,
            'new_stock' => $product->stock,
            'stock' => $product->stock,
        ]);
    }

    /**
     * Bulk update stock for multiple products
     */
    public function bulkUpdateStock(Request $request)
    {
        $seller = $this->ensureSeller($request);

        $data = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $updated = 0;
        foreach ($data['product_ids'] as $productId) {
            $product = Product::find($productId);
            
            if ($product && $product->seller_id === $seller->id) {
                $product->updateStock(
                    $data['type'],
                    $data['quantity'],
                    $data['note'] ?? null,
                    'bulk_update'
                );
                $updated++;
            }
        }

        return response()->json([
            'success' => true,
            'updated_count' => $updated,
            'message' => "{$updated} ürün güncellendi",
        ]);
    }

    /**
     * Get stock history for a product
     */
    public function stockHistory(Request $request, Product $product)
    {
        $seller = $this->ensureSeller($request);
        $this->authorizeSellerProduct($seller, $product);

        $history = $product->stockHistories()
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json([
            'history' => $history,
        ]);
    }

    /**
     * Get the active campaign for a product
     */
    private function getActiveCampaignForProduct($product)
    {
        $campaign = \App\Models\Campaign::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($query) use ($product) {
                // Campaign applies to all products
                $query->whereNull('product_ids')
                    ->whereNull('category_ids')
                    // Or applies to this specific product
                    ->orWhereJsonContains('product_ids', $product->id)
                    // Or applies to this product's category
                    ->orWhereJsonContains('category_ids', $product->category_id);
            })
            ->where(function ($query) {
                // Check usage limit
                $query->whereNull('usage_limit')
                    ->orWhereRaw('usage_count < usage_limit');
            })
            ->orderBy('discount_value', 'desc') // Prioritize higher discounts
            ->first();

        if (!$campaign) {
            return null;
        }

        return [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'type' => $campaign->type,
            'discount_value' => $campaign->discount_value,
            'end_date' => $campaign->end_date->toIso8601String(),
        ];
    }
}