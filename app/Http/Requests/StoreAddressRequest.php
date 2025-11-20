<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'full_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|max:20',
            'is_default' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Adres başlığı zorunludur',
            'title.max' => 'Adres başlığı en fazla 255 karakter olabilir',
            'full_address.required' => 'Adres detayı zorunludur',
            'full_address.max' => 'Adres en fazla 500 karakter olabilir',
            'city.required' => 'Şehir seçimi zorunludur',
            'city.max' => 'Şehir adı en fazla 100 karakter olabilir',
            'district.required' => 'İlçe seçimi zorunludur',
            'district.max' => 'İlçe adı en fazla 100 karakter olabilir',
            'postal_code.max' => 'Posta kodu en fazla 10 karakter olabilir',
            'phone.regex' => 'Geçerli bir telefon numarası giriniz',
            'phone.max' => 'Telefon numarası en fazla 20 karakter olabilir',
        ];
    }
}
