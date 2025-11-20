<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Durumu Güncellendi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #2563eb;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-shipped {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        .status-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .order-details {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .order-details h3 {
            margin-top: 0;
            color: #374151;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #6b7280;
            font-weight: 500;
        }
        .detail-value {
            color: #111827;
            font-weight: 600;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><x-email-icon name="bell" alt="Bildirim" /> Sipariş Durumu Güncellendi</h1>
        </div>
        
        <div class="content">
            @if($recipientType === 'buyer')
                <p>Merhaba,</p>
                <p>Siparişinizin durumu güncellendi.</p>
            @else
                <p>Merhaba {{ $orderItem->product->seller->name }},</p>
                <p>Satış yaptığınız ürünün sipariş durumu güncellendi.</p>
            @endif

            <div style="text-align: center; margin: 30px 0;">
                <span class="status-badge status-{{ $orderItem->status }}">
                    {{ $orderItem->statusTr }}
                </span>
            </div>

            <div class="order-details">
                <h3><x-email-icon name="box" alt="Sipariş" /> Sipariş Detayları</h3>
                <div class="detail-row">
                    <span class="detail-label">Sipariş No:</span>
                    <span class="detail-value">#{{ $orderItem->order_id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Ürün:</span>
                    <span class="detail-value">{{ $orderItem->product->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Miktar:</span>
                    <span class="detail-value">{{ $orderItem->quantity }} adet</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fiyat:</span>
                    <span class="detail-value">{{ number_format($orderItem->price * $orderItem->quantity, 2, ',', '.') }} ₺</span>
                </div>
                @if($recipientType === 'seller')
                    <div class="detail-row">
                        <span class="detail-label">Platform Komisyonu:</span>
                        <span class="detail-value">{{ number_format($orderItem->platform_fee, 2, ',', '.') }} ₺</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Satıcı Kazancı:</span>
                        <span class="detail-value" style="color: #059669;">{{ number_format($orderItem->seller_payout, 2, ',', '.') }} ₺</span>
                    </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Durum:</span>
                    <span class="detail-value">{{ $orderItem->statusTr }}</span>
                </div>
            </div>

            @if($recipientType === 'buyer')
                <div style="text-align: center;">
                    <a href="{{ config('app.frontend_url') }}/orders" class="button">
                        Siparişlerimi Görüntüle
                    </a>
                </div>
                
                <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
                    Siparişinizle ilgili herhangi bir sorunuz varsa, lütfen bizimle iletişime geçin.
                </p>
            @else
                <div style="text-align: center;">
                    <a href="{{ config('app.frontend_url') }}/seller/dashboard" class="button">
                        Satıcı Paneline Git
                    </a>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
