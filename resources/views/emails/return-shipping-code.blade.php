<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İade Kargo Kodunuz</title>
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
            background-color: #10B981;
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
        .code-box {
            background-color: #F3F4F6;
            border: 2px solid #10B981;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            border-radius: 8px;
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            color: #10B981;
            letter-spacing: 2px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #10B981;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .info-box {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6B7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 İade Kargo Kodunuz Hazır</h1>
    </div>
    <div class="content">
        <p>Merhaba {{ $returnRequest->user->name }},</p>
        <p>İade talebiniz (#{{ $returnRequest->return_number }}) için kargo kodunuz oluşturuldu.</p>
        
        <div class="code-box">
            <p style="margin: 0; font-size: 16px; color: #6B7280;">Kargo Firması</p>
            <p style="margin: 5px 0 15px; font-size: 18px; font-weight: bold;">{{ $shippingCarrier }}</p>
            <p style="margin: 0; font-size: 16px; color: #6B7280;">Kargo Takip Kodu</p>
            <p class="code">{{ $shippingCode }}</p>
        </div>
        
        <div class="info-box">
            <p style="margin: 0;"><strong>⚠️ Önemli Bilgiler:</strong></p>
            <ul style="margin: 10px 0 0; padding-left: 20px;">
                <li>Ürünü orijinal ambalajında gönderin</li>
                <li>Ürünün kullanılmamış ve hasarsız olduğundan emin olun</li>
                <li>Fatura ve aksesuarları eksiksiz olarak paketleyin</li>
                <li>Kargo kodunu kargo görevlisine verin</li>
            </ul>
        </div>
        
        <h3>İade Süreci:</h3>
        <ol>
            <li>Ürünü paketleyin</li>
            <li>{{ $shippingCarrier }} şubesine gidin veya kurye çağırın</li>
            <li>Kargo kodunu ({{ $shippingCode }}) kullanın</li>
            <li>Kargo ücretini ödemeyin (kargo ücreti tarafımızdan karşılanacaktır)</li>
            <li>Kargo makbuzunu saklayın</li>
        </ol>
        
        <p>İade durumunuzu takip etmek için:</p>
        
        <div style="text-align: center;">
            <a href="{{ $returnUrl }}" class="button">İade Detaylarını Görüntüle</a>
        </div>
        
        <p>Ürün tarafımıza ulaştığında inceleme yapılacak ve onaylandıktan sonra ödemeniz iade edilecektir.</p>
        
        <p>Herhangi bir sorunuz olursa bizimle iletişime geçmekten çekinmeyin.</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} SportOnline. Tüm hakları saklıdır.</p>
        <p>Bu bir otomatik mesajdır. Lütfen yanıtlamayın.</p>
    </div>
</body>
</html>
