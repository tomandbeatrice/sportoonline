<?php

namespace App\Modules\Ecommerce\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

/**
 * Product Service
 * Handles all product-related business logic
 */
class ProductService
{
    /**
     * Get all products with caching
     */
    public function getAllProducts(array $filters = []): Collection
    {
        $cacheKey = 'products.all.' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($filters) {
            $query = Product::query();
            
            if (isset($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }
            
            if (isset($filters['search'])) {
                $query->where('name', 'like', "%{$filters['search']}%");
            }
            
            if (isset($filters['min_price'])) {
                $query->where('price', '>=', $filters['min_price']);
            }
            
            if (isset($filters['max_price'])) {
                $query->where('price', '<=', $filters['max_price']);
            }
            
            return $query->with(['category', 'seller'])->get();
        });
    }
    
    /**
     * Get single product with caching
     */
    public function getProduct(int $id): ?Product
    {
        return Cache::remember("product.{$id}", now()->addHours(2), function () use ($id) {
            return Product::with(['category', 'seller', 'reviews'])->find($id);
        });
    }
    
    /**
     * Create new product
     */
    public function createProduct(array $data): Product
    {
        $product = Product::create($data);
        
        // Clear cache
        $this->clearProductCache();
        
        return $product;
    }
    
    /**
     * Update product
     */
    public function updateProduct(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        
        // Clear specific product cache
        Cache::forget("product.{$id}");
        $this->clearProductCache();
        
        return $product->fresh();
    }
    
    /**
     * Delete product
     */
    public function deleteProduct(int $id): bool
    {
        $product = Product::findOrFail($id);
        $result = $product->delete();
        
        // Clear cache
        Cache::forget("product.{$id}");
        $this->clearProductCache();
        
        return $result;
    }
    
    /**
     * Check stock availability
     */
    public function checkStock(int $productId, int $quantity): bool
    {
        $product = $this->getProduct($productId);
        
        return $product && $product->stock >= $quantity;
    }
    
    /**
     * Reduce stock
     */
    public function reduceStock(int $productId, int $quantity): void
    {
        $product = Product::findOrFail($productId);
        
        if ($product->stock < $quantity) {
            throw new \Exception("Insufficient stock for product {$productId}");
        }
        
        $product->decrement('stock', $quantity);
        
        // Clear cache
        Cache::forget("product.{$productId}");
    }
    
    /**
     * Clear all product caches
     */
    protected function clearProductCache(): void
    {
        Cache::tags(['products'])->flush();
    }
}
