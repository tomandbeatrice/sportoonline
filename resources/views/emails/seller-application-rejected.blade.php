<x-mail::message>
# Başvurunuz Hakkında

Merhaba {{ $application->first_name }},

**{{ $application->company_name }}** adına yaptığınız satıcı başvurusu incelenmiştir.

Maalesef başvurunuz şu anda onaylanamamıştır.

## Red Sebebi

{{ $application->rejection_reason }}

## Tekrar Başvuru

Yukarıdaki durumu düzelttikten sonra tekrar başvuru yapabilirsiniz. Eksik bilgilerinizi tamamlayarak yeni bir başvuru gönderebilirsiniz.

<x-mail::button :url="url('/seller/register')">
Yeni Başvuru Yap
</x-mail::button>

Herhangi bir sorunuz olursa bizimle iletişime geçmekten çekinmeyin.

<x-mail::button :url="$contactUrl">
İletişime Geç
</x-mail::button>

Anlayışınız için teşekkür ederiz.

Saygılarımızla,<br>
{{ config('app.name') }} Ekibi
</x-mail::message>
