<x-mail::message>
# Stok Uyarısı ⚠️

Merhaba {{ $seller->name }},

Aşağıdaki ürünlerinizde stok azlığı tespit edildi:

<x-mail::table>
| Ürün | Mevcut Stok | Durum |
|:-----|:-----------:|:-----:|
@foreach($products as $product)
| {{ $product->name }} | {{ $product->stock }} | @if($product->stock == 0) ❌ Tükendi @else ⚠️ Azalıyor @endif |
@endforeach
</x-mail::table>

Satış kaybı yaşamamak için stokları en kısa sürede güncellemenizi öneririz.

<x-mail::button :url="$stockUrl">
Stok Yönetimi
</x-mail::button>

İyi satışlar,<br>
{{ config('app.name') }}
</x-mail::message>
