<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
            'phone' => 'sometimes|string|regex:/^([0-9\s\-\+\(\)]*)$/|max:20',
            'birth_date' => 'sometimes|date|before:today',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.string' => 'Ad geçerli bir metin olmalıdır',
            'first_name.max' => 'Ad en fazla 255 karakter olabilir',
            'last_name.string' => 'Soyad geçerli bir metin olmalıdır',
            'last_name.max' => 'Soyad en fazla 255 karakter olabilir',
            'email.email' => 'Geçerli bir e-posta adresi giriniz',
            'email.unique' => 'Bu e-posta adresi kullanılmaktadır',
            'phone.regex' => 'Geçerli bir telefon numarası giriniz',
            'phone.max' => 'Telefon numarası en fazla 20 karakter olabilir',
            'birth_date.date' => 'Geçerli bir tarih giriniz',
            'birth_date.before' => 'Doğum tarihi bugünden önce olmalıdır',
        ];
    }
}
