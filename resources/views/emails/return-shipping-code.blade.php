@component('mail::message')
# İade Kargo Kodu

Merhaba {{ $returnRequest->user->name }},

**İade #{{ $returnRequest->return_number }}** için kargo kodunuz hazır!

Ürününüzü aşağıdaki kargo bilgileri ile gönderebilirsiniz:

**Kargo Firması:** {{ $carrier }}  
**Takip Numarası:** {{ $shippingCode }}

@component('mail::button', ['url' => $trackingUrl])
Kargonu Takip Et
@endcomponent

**İade Bilgileri:**
- **İade Numarası:** {{ $returnRequest->return_number }}
- **Sipariş Numarası:** #{{ $returnRequest->order_id }}
- **İade Tutarı:** ₺{{ number_format($returnRequest->refund_amount, 2) }}

**Önemli Notlar:**
- Ürünü orijinal ambalajında ve eksiksiz olarak göndermeniz gerekmektedir.
- Kargo ücreti tarafımızdan karşılanmaktadır.
- Ürün tarafımıza ulaştığında inceleme süreci başlayacaktır.

Herhangi bir sorunuz varsa, müşteri hizmetlerimizle iletişime geçebilirsiniz.

Teşekkürler,<br>
{{ config('app.name') }} Ekibi
@endcomponent
