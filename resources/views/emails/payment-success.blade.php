<x-mail::message>
# Ödemeniz Başarıyla Alındı <x-email-icon name="check" alt="Başarılı" />

Merhaba {{ $order->user->name }},

**#{{ $order->id }}** numaralı siparişiniz için ödemeniz başarıyla tamamlandı.

**Ödeme Bilgileri:**
- İşlem Tarihi: {{ $transaction->created_at->format('d.m.Y H:i') }}
- Ödeme Yöntemi: {{ $transaction->gateway }}
- İşlem No: {{ $transaction->transaction_id }}
- Tutar: ₺{{ number_format($transaction->amount, 2) }}

Siparişiniz hazırlanmaya başlandı. Kargoya verildiğinde size bilgi vereceğiz.

<x-mail::button :url="$orderUrl">
Siparişimi Takip Et
</x-mail::button>

Teşekkür ederiz,<br>
{{ config('app.name') }}
</x-mail::message>
