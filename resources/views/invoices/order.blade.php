<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .company-info h1 {
            color: #2563eb;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .company-info p {
            color: #666;
            font-size: 11px;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h2 {
            font-size: 32px;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .invoice-date {
            font-size: 11px;
            color: #999;
        }
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .party-box {
            width: 48%;
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
        }
        .party-box h3 {
            font-size: 14px;
            color: #2563eb;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .party-box p {
            margin-bottom: 6px;
            font-size: 11px;
        }
        .party-box strong {
            display: inline-block;
            width: 80px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        thead {
            background: #2563eb;
            color: white;
        }
        thead th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tbody td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        tbody tr:last-child td {
            border-bottom: 2px solid #2563eb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .product-name {
            font-weight: 600;
            color: #111;
            margin-bottom: 3px;
        }
        .product-desc {
            color: #666;
            font-size: 10px;
        }
        .totals {
            margin-left: auto;
            width: 350px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .total-row.subtotal {
            background: #f9fafb;
        }
        .total-row.shipping {
            background: #f9fafb;
        }
        .total-row.tax {
            background: #f9fafb;
        }
        .total-row.discount {
            background: #fef3c7;
            color: #92400e;
        }
        .total-row.grand-total {
            background: #2563eb;
            color: white;
            font-weight: bold;
            font-size: 16px;
            border: none;
            margin-top: 5px;
        }
        .total-label {
            font-weight: 600;
        }
        .total-value {
            font-weight: 600;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
        }
        .footer-notes {
            background: #fef3c7;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: left;
        }
        .footer-notes h4 {
            color: #92400e;
            margin-bottom: 8px;
            font-size: 12px;
        }
        .footer-notes p {
            color: #78350f;
            font-size: 10px;
            line-height: 1.5;
        }
        .footer-info {
            font-size: 10px;
            color: #999;
            line-height: 1.8;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-flex">
                <div class="company-info">
                    <h1>{{ config('app.name', 'SportOnline') }}</h1>
                    <p>E-Ticaret Platformu</p>
                    <p>www.sportoonline.com | info@sportoonline.com</p>
                    <p>Tel: +90 (212) 555-0000</p>
                </div>
                <div class="invoice-info">
                    <h2>FATURA</h2>
                    <div class="invoice-number">#{{ str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</div>
                    <div class="invoice-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                    <div style="margin-top: 10px;">
                        <span class="status-badge status-{{ $order->status }}">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing & Shipping Info -->
        <div class="parties">
            <div class="party-box">
                <h3>📍 Fatura Bilgileri</h3>
                <p><strong>Ad Soyad:</strong> {{ $order->user->name ?? 'Müşteri' }}</p>
                <p><strong>E-posta:</strong> {{ $order->user->email ?? $order->email }}</p>
                <p><strong>Telefon:</strong> {{ $order->user->phone ?? $order->phone ?? 'Belirtilmemiş' }}</p>
                <p><strong>Adres:</strong> {{ $order->address ?? 'Belirtilmemiş' }}</p>
            </div>
            <div class="party-box">
                <h3>🚚 Teslimat Bilgileri</h3>
                <p><strong>Alıcı:</strong> {{ $order->shipping_name ?? $order->user->name ?? 'Müşteri' }}</p>
                <p><strong>Telefon:</strong> {{ $order->shipping_phone ?? $order->phone ?? 'Belirtilmemiş' }}</p>
                <p><strong>Adres:</strong> {{ $order->shipping_address ?? $order->address ?? 'Belirtilmemiş' }}</p>
                @if($order->tracking_code)
                <p><strong>Kargo:</strong> {{ $order->shipping_provider ?? 'Belirtilmemiş' }}</p>
                <p><strong>Takip No:</strong> {{ $order->tracking_code }}</p>
                @endif
            </div>
        </div>

        <!-- Order Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Ürün Bilgileri</th>
                    <th class="text-center" style="width: 80px;">Adet</th>
                    <th class="text-right" style="width: 100px;">Birim Fiyat</th>
                    <th class="text-right" style="width: 120px;">Toplam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="product-name">{{ $item->product->name ?? 'Ürün' }}</div>
                        @if($item->product->sku)
                        <div class="product-desc">SKU: {{ $item->product->sku }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₺{{ number_format($item->price, 2, ',', '.') }}</td>
                    <td class="text-right">₺{{ number_format($item->quantity * $item->price, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row subtotal">
                <span class="total-label">Ara Toplam</span>
                <span class="total-value">₺{{ number_format($order->items->sum(function($item) { return $item->quantity * $item->price; }), 2, ',', '.') }}</span>
            </div>
            
            @if($order->shipping_cost > 0)
            <div class="total-row shipping">
                <span class="total-label">Kargo Ücreti</span>
                <span class="total-value">₺{{ number_format($order->shipping_cost, 2, ',', '.') }}</span>
            </div>
            @endif

            @if($order->tax > 0)
            <div class="total-row tax">
                <span class="total-label">KDV (%{{ $order->tax_rate ?? 20 }})</span>
                <span class="total-value">₺{{ number_format($order->tax, 2, ',', '.') }}</span>
            </div>
            @endif

            @if($order->discount_amount > 0)
            <div class="total-row discount">
                <span class="total-label">💰 İndirim</span>
                <span class="total-value">-₺{{ number_format($order->discount_amount, 2, ',', '.') }}</span>
            </div>
            @endif

            <div class="total-row grand-total">
                <span class="total-label">GENEL TOPLAM</span>
                <span class="total-value">₺{{ number_format($order->total, 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            @if($order->notes)
            <div class="footer-notes">
                <h4>📝 Sipariş Notu</h4>
                <p>{{ $order->notes }}</p>
            </div>
            @endif

            <div class="footer-info">
                <p><strong>{{ config('app.name', 'SportOnline') }}</strong> - E-Ticaret Platformu</p>
                <p>Vergi Dairesi: Beyoğlu | Vergi No: 1234567890</p>
                <p>Adres: Örnek Mahallesi, Test Sokak No:1, Beyoğlu/İstanbul</p>
                <p style="margin-top: 15px; color: #2563eb;">
                    Ödemenizi aldığımızı beyan eder, teşekkür ederiz.
                </p>
                <p style="margin-top: 10px; font-size: 9px;">
                    Bu fatura {{ now()->format('d.m.Y H:i') }} tarihinde elektronik ortamda oluşturulmuştur.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
