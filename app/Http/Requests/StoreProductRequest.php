<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only sellers can create products
        return auth()->check() && auth()->user()->role === 'seller';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku|max:100',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ürün adı zorunludur',
            'name.max' => 'Ürün adı en fazla 255 karakter olabilir',
            'description.required' => 'Ürün açıklaması zorunludur',
            'description.min' => 'Ürün açıklaması en az 20 karakter olmalıdır',
            'short_description.max' => 'Kısa açıklama en fazla 500 karakter olabilir',
            'price.required' => 'Fiyat zorunludur',
            'price.numeric' => 'Fiyat sayısal bir değer olmalıdır',
            'price.min' => 'Fiyat 0 veya daha büyük olmalıdır',
            'sale_price.numeric' => 'İndirimli fiyat sayısal bir değer olmalıdır',
            'sale_price.lt' => 'İndirimli fiyat normal fiyattan düşük olmalıdır',
            'category_id.required' => 'Kategori seçimi zorunludur',
            'category_id.exists' => 'Geçersiz kategori',
            'stock.required' => 'Stok miktarı zorunludur',
            'stock.integer' => 'Stok tam sayı olmalıdır',
            'stock.min' => 'Stok 0 veya daha büyük olmalıdır',
            'sku.unique' => 'Bu SKU kodu kullanılmaktadır',
            'sku.max' => 'SKU en fazla 100 karakter olabilir',
            'images.max' => 'En fazla 10 görsel yükleyebilirsiniz',
            'images.*.image' => 'Sadece resim dosyaları yükleyebilirsiniz',
            'images.*.mimes' => 'Görsel formatı jpg, jpeg, png veya webp olmalıdır',
            'images.*.max' => 'Her görsel en fazla 5MB olabilir',
            'tags.*.max' => 'Her etiket en fazla 50 karakter olabilir',
        ];
    }
}
