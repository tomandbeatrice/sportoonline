<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Yetki kontrolü yapılmayacaksa bu şekilde kalabilir
    }

    public function rules(): array
    {
        return [
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'price'         => 'required|decimal:0,2|min:0', // Laravel 10+ ile gelen decimal desteği
            'stock'         => 'required|integer|min:0',
            'category_id'   => 'required|exists:categories,id',

            // Opsiyonel Alanlar:
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpg,jpeg,png|max:2048', // Her görsel için kurallar
            'variants'      => 'nullable|array',
            'variants.*.name'  => 'required_with:variants|string|max:100',
            'variants.*.price' => 'nullable|decimal:0,2|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'Ürün başlığı zorunludur.',
            'price.required'        => 'Fiyat bilgisi girilmelidir.',
            'price.decimal'         => 'Fiyat rakamsal ve en fazla 2 ondalıklı olmalıdır.',
            'stock.required'        => 'Stok bilgisi girilmelidir.',
            'category_id.required'  => 'Kategori seçilmelidir.',
            'category_id.exists'    => 'Seçilen kategori bulunamadı.',
            'images.*.image'        => 'Yüklenen dosya geçerli bir görsel olmalıdır.',
            'images.*.mimes'        => 'Görseller yalnızca JPG, JPEG veya PNG formatında olabilir.',
            'variants.*.name.required_with' => 'Varyant adı gereklidir.',
        ];
    }
}