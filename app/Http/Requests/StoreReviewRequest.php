<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller (verified purchase)
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string|min:10|max:2000',
            'pros' => 'nullable|string|max:500',
            'cons' => 'nullable|string|max:500',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max per image
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Ürün seçimi zorunludur',
            'product_id.exists' => 'Geçersiz ürün',
            'rating.required' => 'Puan zorunludur',
            'rating.min' => 'En az 1 yıldız vermelisiniz',
            'rating.max' => 'En fazla 5 yıldız verebilirsiniz',
            'title.required' => 'Başlık zorunludur',
            'title.max' => 'Başlık en fazla 255 karakter olabilir',
            'comment.required' => 'Yorum zorunludur',
            'comment.min' => 'Yorum en az 10 karakter olmalıdır',
            'comment.max' => 'Yorum en fazla 2000 karakter olabilir',
            'pros.max' => 'Artılar en fazla 500 karakter olabilir',
            'cons.max' => 'Eksiler en fazla 500 karakter olabilir',
            'photos.max' => 'En fazla 5 fotoğraf yükleyebilirsiniz',
            'photos.*.image' => 'Sadece resim dosyaları yükleyebilirsiniz',
            'photos.*.mimes' => 'Fotoğraf formatı jpg, jpeg, png veya webp olmalıdır',
            'photos.*.max' => 'Her fotoğraf en fazla 5MB olabilir',
        ];
    }
}
