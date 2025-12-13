<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İade Kargo Kodu</title>
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
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .shipping-info {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #4F46E5;
            border-radius: 4px;
        }
        .shipping-code {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>İade Kargo Kodu</h1>
    </div>
    
    <div class="content">
        <p>Merhaba {{ $customerName }},</p>
        
        <p>İade talebiniz için kargo kodu oluşturulmuştur. Aşağıdaki bilgileri kullanarak ürününüzü kargoya verebilirsiniz.</p>
        
        <div class="shipping-info">
            <h3 style="margin-top: 0;">Kargo Bilgileri</h3>
            <p><strong>Kargo Firması:</strong> {{ $shippingCarrier }}</p>
            <p><strong>Kargo Takip Kodu:</strong></p>
            <div class="shipping-code">{{ $shippingCode }}</div>
            <p><strong>Ürün:</strong> {{ $productName }}</p>
            <p><strong>İade Talep No:</strong> #{{ $return->id }}</p>
        </div>
        
        <h3>Yapmanız Gerekenler:</h3>
        <ol>
            <li>Ürünü orijinal ambalajında paketleyin</li>
            <li>Yukarıdaki kargo kodunu kullanarak en yakın {{ $shippingCarrier }} şubesine teslim edin</li>
            <li>Kargo teslim fişini saklayın</li>
            <li>İade sürecini hesabınızdan takip edebilirsiniz</li>
        </ol>
        
        <p style="margin-top: 20px;">
            <a href="{{ config('app.url') }}/user/returns/{{ (int)$return->id }}" class="button">
                İade Detayını Görüntüle
            </a>
        </p>
        
        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
