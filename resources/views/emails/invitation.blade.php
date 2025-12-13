<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
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
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .code {
            background-color: #e8f5e9;
            padding: 15px;
            border-left: 4px solid #4CAF50;
            margin: 15px 0;
            font-family: monospace;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sportoonline Daveti</h1>
    </div>
    <div class="content">
        <h2>Merhaba!</h2>
        <p>{{ $invitation->inviter->name ?? 'Bir arkadaşınız' }} sizi Sportoonline platformuna davet etti.</p>
        
        <p>Sportoonline, spor ürünleri, hizmetler ve daha fazlasını bulabileceğiniz kapsamlı bir çevrimiçi pazaryeridir.</p>
        
        <p>Daveti kabul etmek için aşağıdaki butona tıklayın:</p>
        
        <div style="text-align: center;">
            <a href="{{ $acceptUrl }}" class="button">Daveti Kabul Et</a>
        </div>
        
        <p>Veya aşağıdaki kodu kullanarak manuel olarak kaydolabilirsiniz:</p>
        
        <div class="code">
            Davet Kodu: {{ $invitation->code }}
        </div>
        
        <p><strong>Not:</strong> Bu davet {{ $invitation->expires_at->format('d.m.Y H:i') }} tarihine kadar geçerlidir.</p>
    </div>
    <div class="footer">
        <p>Bu e-posta Sportoonline tarafından otomatik olarak gönderilmiştir.</p>
        <p>&copy; {{ date('Y') }} Sportoonline. Tüm hakları saklıdır.</p>
    </div>
</body>
</html>
