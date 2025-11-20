<x-mail::message>
# Ödeme İşlemi Başarısız <x-email-icon name="cross" alt="Başarısız" />

Merhaba {{ $order->user->name }},

Maalesef **#{{ $order->id }}** numaralı siparişiniz için ödeme işlemi başarısız oldu.

**Hata Detayları:**
{{ $errorMessage }}

**Ne yapmalısınız?**
- Kart bilgilerinizi kontrol edin
- Kartınızda yeterli bakiye olduğundan emin olun
- Farklı bir ödeme yöntemi deneyin

Siparişinizi tamamlamak için tekrar deneyebilirsiniz.

<x-mail::button :url="$checkoutUrl">
Tekrar Dene
</x-mail::button>

Yardıma ihtiyacınız varsa bizimle iletişime geçin.

Saygılarımızla,<br>
{{ config('app.name') }}
</x-mail::message>
