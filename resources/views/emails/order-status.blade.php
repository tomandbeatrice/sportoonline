@component('mail::message')
# Sipariş Durumu Güncellendi

Merhaba {{ $order->user->name }},

**Sipariş #{{ $order->id }}** numaralı siparişinizin durumu güncellendi.

**Eski Durum:** {{ $oldStatusLabel }}  
**Yeni Durum:** {{ $newStatusLabel }}

@if($order->status === 'shipped' && $order->tracking_number)
**Kargo Takip Numarası:** {{ $order->tracking_number }}
@endif

@component('mail::button', ['url' => $orderUrl])
Siparişi Görüntüle
@endcomponent

**Sipariş Özeti:**
- **Toplam Tutar:** ₺{{ number_format($order->total_amount, 2) }}
- **Sipariş Tarihi:** {{ $order->created_at->format('d.m.Y H:i') }}

Herhangi bir sorunuz varsa, müşteri hizmetlerimizle iletişime geçebilirsiniz.

Teşekkürler,<br>
{{ config('app.name') }} Ekibi
@endcomponent
