<x-mail::message>
# <x-email-icon name="party" alt="Tebrik" /> Siparişiniz Alındı!

Merhaba {{ $order->user->name }},

**#{{ $order->id }}** numaralı siparişiniz başarıyla alındı.

## Sipariş Detayları

<x-mail::table>
| Ürün | Miktar | Fiyat |
|:-----|:------:|------:|
@foreach($order->orderDetails as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ₺{{ number_format($item->price, 2) }} |
@endforeach
</x-mail::table>

**Toplam Tutar:** ₺{{ number_format($order->total_amount, 2) }}
@if($order->discount_amount > 0)
**İndirim:** -₺{{ number_format($order->discount_amount, 2) }}
@endif

**Teslimat Adresi:**
{{ $order->address->address_line }}, {{ $order->address->city }}

<x-mail::button :url="$orderUrl">
Siparişimi Görüntüle
</x-mail::button>

Siparişinizle ilgili güncellemeler e-posta yoluyla size bildirilecektir.

Teşekkür ederiz,<br>
{{ config('app.name') }}
</x-mail::message>
