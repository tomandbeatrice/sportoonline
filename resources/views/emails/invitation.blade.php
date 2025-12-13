<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Davetiye</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #3B82F6;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #fff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #3B82F6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6B7280;
            font-size: 14px;
        }
        .code-box {
            background-color: #F3F4F6;
            border: 2px dashed #9CA3AF;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 SportOnline'a Davetlisiniz!</h1>
    </div>
    <div class="content">
        <p>Merhaba,</p>
        <p><strong>{{ $inviterName }}</strong> sizi SportOnline platformuna katılmaya davet ediyor!</p>
        <p>SportOnline, spor ürünleri, hizmetler ve daha fazlasını bulabileceğiniz kapsamlı bir pazaryeridir.</p>
        
        <p><strong>Davet Kodunuz:</strong></p>
        <div class="code-box">{{ $code }}</div>
        
        <p>Aşağıdaki butona tıklayarak daveti kabul edebilir ve kayıt olabilirsiniz:</p>
        
        <div style="text-align: center;">
            <a href="{{ $acceptUrl }}" class="button">Daveti Kabul Et</a>
        </div>
        
        <p><strong>Not:</strong> Bu davet <strong>{{ $expiresAt }}</strong> tarihine kadar geçerlidir.</p>
        
        <p>Eğer siz bu daveti talep etmediyseniz, bu e-postayı görmezden gelebilirsiniz.</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} SportOnline. Tüm hakları saklıdır.</p>
        <p>Bu bir otomatik mesajdır. Lütfen yanıtlamayın.</p>
    </div>
</body>
</html>
