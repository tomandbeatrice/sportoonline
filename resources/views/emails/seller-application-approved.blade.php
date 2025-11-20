<x-mail::message>
# <x-email-icon name="party" alt="Tebrik" /> Tebrikler! Başvurunuz Onaylandı

Merhaba {{ $application->first_name }},

Harika haberler! **{{ $application->company_name }}** adına yaptığınız satıcı başvurusu onaylanmıştır.

## Hesap Bilgileriniz

- **E-posta:** {{ $user->email }}
- **Rol:** Satıcı
- **Durum:** Aktif

Hesabınız oluşturulmuştur. Giriş yapabilmek için önce şifrenizi belirlemeniz gerekmektedir.

<x-mail::button :url="$resetPasswordUrl">
Şifre Belirle
</x-mail::button>

## Başlangıç Adımları

1. ✅ Şifrenizi belirleyin
2. 🏪 Satıcı panelinize giriş yapın
3. 📦 İlk ürünlerinizi ekleyin
4. 🎯 Kampanyalar oluşturun
5. <x-email-icon name="rocket" alt="Başla" /> Satışa başlayın!

<x-mail::panel>
**Önemli:** Şifre belirleme linkinin geçerlilik süresi 24 saattir. Bu süre içinde şifrenizi belirlemeyi unutmayın.
</x-mail::panel>

## Satıcı Paneli

Şifrenizi belirledikten sonra aşağıdaki linkten satıcı panelinize erişebilirsiniz:

<x-mail::button :url="$loginUrl">
Satıcı Paneline Git
</x-mail::button>

Satıcı panelinde yapabilecekleriniz:
- Ürün ekleme ve yönetimi
- Stok takibi
- Sipariş yönetimi
- Kampanya oluşturma
- Finansal raporlar

Başarılar dileriz! <x-email-icon name="rocket" alt="Başarı" />

Saygılarımızla,<br>
{{ config('app.name') }} Ekibi
</x-mail::message>
