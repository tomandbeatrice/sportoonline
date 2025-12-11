@component('mail::message')
# SportoOnline Daveti

Merhaba,

**{{ $inviterName }}** seni SportoOnline'a davet ediyor!

SportoOnline'da spor ürünleri, yemek siparişi, otel rezervasyonu ve daha fazlası için tek platformda buluşabilirsin.

@component('mail::button', ['url' => $acceptUrl])
Daveti Kabul Et
@endcomponent

**Davet Kodu:** {{ $invitationCode }}

**Son Geçerlilik Tarihi:** {{ $expiresAt }}

Eğer bu davet senin için değilse, bu emaili görmezden gelebilirsin.

Teşekkürler,<br>
{{ config('app.name') }} Ekibi
@endcomponent
