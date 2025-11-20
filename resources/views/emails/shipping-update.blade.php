<x-mail::message>
# <x-email-icon name="box" alt="Kargo" /> Siparişiniz Kargoya Verildi

Merhaba {{ $order->user->name }},

Harika haber! **#{{ $order->id }}** numaralı siparişiniz kargoya verildi.

**Kargo Bilgileri:**
- Kargo Firması: {{ $order->shipping_company }}
- Takip No: **{{ $order->tracking_number }}**
- Tahmini Teslimat: 2-3 iş günü

Kargo takip numaranızı kullanarak paketinizin nerede olduğunu öğrenebilirsiniz.

<x-mail::button :url="$trackingUrl">
Kargoyu Takip Et
</x-mail::button>

**Teslimat Adresi:**
{{ $order->address->address_line }}
{{ $order->address->city }}, {{ $order->address->postal_code }}

İyi alışverişler dileriz,<br>
{{ config('app.name') }}
</x-mail::message>
