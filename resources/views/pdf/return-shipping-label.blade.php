<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        .label-container {
            width: 100%;
            border: 2px solid #000;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 5px;
            margin-bottom: 5px;
            border-left: 4px solid #333;
        }
        .address-box {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 80px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .barcode {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 24px;
            margin: 15px 0;
            padding: 10px;
            border: 2px dashed #000;
        }
        .tracking-number {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .column {
            display: table-cell;
            width: 50%;
            padding: 5px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="header">
            <h1>İADE KARGO ETİKETİ</h1>
            <p>Return Shipping Label</p>
        </div>

        <div class="tracking-number">
            Takip No / Tracking: {{ $tracking_number }}
        </div>

        <div class="barcode">
            {{ $barcode }}
        </div>

        <div class="two-column">
            <div class="column">
                <div class="section">
                    <div class="section-title">GÖNDEREN / SENDER</div>
                    <div class="address-box">
                        <div class="info-row"><strong>{{ $sender_name }}</strong></div>
                        <div class="info-row">{{ $sender_address }}</div>
                        <div class="info-row">{{ $sender_district }} / {{ $sender_city }}</div>
                        @if($sender_postal_code)
                        <div class="info-row">Posta Kodu: {{ $sender_postal_code }}</div>
                        @endif
                        <div class="info-row">Tel: {{ $sender_phone }}</div>
                    </div>
                </div>
            </div>

            <div class="column">
                <div class="section">
                    <div class="section-title">ALICI / RECEIVER</div>
                    <div class="address-box">
                        <div class="info-row"><strong>{{ $receiver_name }}</strong></div>
                        <div class="info-row">{{ $receiver_address }}</div>
                        <div class="info-row">{{ $receiver_district }} / {{ $receiver_city }}</div>
                        @if($receiver_postal_code)
                        <div class="info-row">Posta Kodu: {{ $receiver_postal_code }}</div>
                        @endif
                        <div class="info-row">Tel: {{ $receiver_phone }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">ÜRÜN BİLGİLERİ / PRODUCT INFORMATION</div>
            <div style="padding: 10px;">
                <div class="info-row"><span class="label">Ürün:</span> {{ $product_name }}</div>
                <div class="info-row"><span class="label">Miktar:</span> {{ $quantity }}</div>
                <div class="info-row"><span class="label">İade No:</span> {{ $return_number }}</div>
                <div class="info-row"><span class="label">Sipariş No:</span> #{{ $order_id }}</div>
            </div>
        </div>

        <div class="footer">
            <p>Etiket Oluşturma Tarihi: {{ $generated_at->format('d.m.Y H:i') }}</p>
            <p>İade Talebi Tarihi: {{ $created_at->format('d.m.Y H:i') }}</p>
            <p>&copy; {{ date('Y') }} Sportoonline - www.sportoonline.com</p>
        </div>
    </div>
</body>
</html>
