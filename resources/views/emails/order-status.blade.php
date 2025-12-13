<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Durumu Güncellendi</title>
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
        .status-box {
            background-color: #F3F4F6;
            border-left: 4px solid #3B82F6;
            padding: 15px;
            margin: 20px 0;
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
        .order-details {
            background-color: #F9FAFB;
            padding: 15px;
            border-radius: 6px;
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
        <h1>📦 Sipariş Durumunuz Güncellendi</h1>
    </div>
    <div class="content">
        <p>Merhaba {{ $order->user->name }},</p>
        <p>Sipariş numarası <strong>#{{ $order->id }}</strong> olan siparişinizin durumu güncellendi.</p>
        
        <div class="status-box">
            <p style="margin: 0;"><strong>Önceki Durum:</strong> {{ $oldStatus }}</p>
            <p style="margin: 10px 0 0;"><strong>Yeni Durum:</strong> <span style="color: #3B82F6; font-weight: bold;">{{ $newStatus }}</span></p>
        </div>
        
        <div class="order-details">
            <h3 style="margin-top: 0;">Sipariş Detayları</h3>
            <p><strong>Sipariş No:</strong> #{{ $order->id }}</p>
            <p><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
            <p><strong>Toplam Tutar:</strong> ₺{{ number_format($order->total_amount, 2) }}</p>
        </div>
        
        @if($order->status === 'shipped' && $order->tracking_number)
        <div class="status-box">
            <p><strong>🚚 Kargo Takip Numarası:</strong> {{ $order->tracking_number }}</p>
        </div>
        @endif
        
        <p>Siparişinizin detaylarını görüntülemek için:</p>
        
        <div style="text-align: center;">
            <a href="{{ $orderUrl }}" class="button">Siparişi Görüntüle</a>
        </div>
        
        <p>Herhangi bir sorunuz varsa, müşteri hizmetlerimizle iletişime geçmekten çekinmeyin.</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} SportOnline. Tüm hakları saklıdır.</p>
        <p>Bu bir otomatik mesajdır. Lütfen yanıtlamayın.</p>
    </div>
</body>
</html>
