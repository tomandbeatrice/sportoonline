<x-mail::message>
# <x-email-icon name="mail" alt="Başvuru" /> Başvurunuz Alındı!

Merhaba {{ $application->first_name }},

**{{ $application->company_name }}** adına yaptığınız satıcı başvurusu başarıyla alınmıştır.

## Başvuru Bilgileri

- **Firma Adı:** {{ $application->company_name }}
- **E-posta:** {{ $application->email }}
- **Telefon:** {{ $application->phone }}
- **Başvuru Tarihi:** {{ $application->created_at->format('d.m.Y H:i') }}

Başvurunuz en kısa sürede incelenecek ve sonuç size e-posta ile bildirilecektir. Genellikle 1-3 iş günü içinde geri dönüş sağlanmaktadır.

## Sonraki Adımlar

1. <x-email-icon name="check" alt="Alındı" /> Başvurunuz alındı
2. ⏳ İnceleme süreci (1-3 iş günü)
3. <x-email-icon name="mail" alt="Bildirim" /> Sonuç bildirimi
4. <x-email-icon name="party" alt="Onay" /> Onay durumunda hesap oluşturma

Herhangi bir sorunuz varsa bizimle iletişime geçebilirsiniz.

Teşekkür ederiz,<br>
{{ config('app.name') }} Ekibi
</x-mail::message>
