<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SportOnline Daveti</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4F46E5;">SportOnline'a Davet Edildiniz!</h2>
        
        <p>Merhaba,</p>
        
        <p>{{ $invitation->user->name }} sizi SportOnline'a davet etti!</p>
        
        <p>SportOnline, spor ürünleri ve hizmetler için Türkiye'nin lider çok amaçlı pazaryeridir.</p>
        
        <p style="margin: 30px 0;">
            <a href="{{ $inviteUrl }}" 
               style="display: inline-block; padding: 12px 30px; background-color: #4F46E5; color: white; text-decoration: none; border-radius: 5px;">
                Hemen Katıl
            </a>
        </p>
        
        <p>Veya bu bağlantıyı tarayıcınıza kopyalayın:</p>
        <p style="color: #666; font-size: 14px;">{{ $inviteUrl }}</p>
        
        <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            Bu davet {{ $invitation->expires_at->format('d.m.Y H:i') }} tarihine kadar geçerlidir.
        </p>
        
        <p style="color: #666; font-size: 12px;">
            SportOnline Ekibi
        </p>
    </div>
</body>
</html>
