<x-mail::message>
# <x-email-icon name="party" alt="Kampanya" strategy="img" /> Kampanya Sona Eriyor!

Merhaba {{ $user->name }},

**{{ $campaign->name }}** kampanyası yakında sona eriyor!

@if($campaign->type === 'percentage')
**%{{ $campaign->discount_value }} İndirim** fırsatından yararlanmak için acele edin!
@else
**₺{{ $campaign->discount_value }} İndirim** fırsatından yararlanmak için acele edin!
@endif

**Kampanya Bitiş Tarihi:** {{ $campaign->end_date->format('d.m.Y H:i') }}

@if($campaign->coupon_code)
**Kupon Kodu:** `{{ $campaign->coupon_code }}`
@endif

Bu harika fırsatı kaçırmayın!

<x-mail::button :url="$shopUrl">
Alışverişe Başla
</x-mail::button>

İyi alışverişler,<br>
{{ config('app.name') }}
</x-mail::message>
