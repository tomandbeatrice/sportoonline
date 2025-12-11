<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kargo Etiketi - {{ $returnRequest->return_number }}</title>
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
            max-width: 600px;
            border: 2px solid #000;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #333;
            text-transform: uppercase;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .barcode-section {
            text-align: center;
            padding: 20px;
            background: #f5f5f5;
            margin: 20px 0;
        }
        .barcode-img {
            max-width: 100%;
            height: auto;
        }
        .return-number {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 10px;
        }
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <!-- Header -->
        <div class="header">
            <h1>İADE KARGO ETİKETİ</h1>
            <p>SportoOnline</p>
        </div>

        <!-- Barcode Section -->
        <div class="barcode-section">
            <img src="{{ $barcode }}" alt="Barcode" class="barcode-img">
            <div class="return-number">{{ $returnRequest->return_number }}</div>
        </div>

        <!-- Sender Information (Customer) -->
        <div class="section">
            <div class="section-title">GÖNDEREN BİLGİLERİ</div>
            <div class="info-row">
                <span class="info-label">Ad Soyad:</span>
                <span>{{ $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Telefon:</span>
                <span>{{ $user->phone ?? 'Belirtilmemiş' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span>{{ $user->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Adres:</span>
                <span>{{ $order->shipping_address ?? 'Belirtilmemiş' }}</span>
            </div>
        </div>

        <!-- Receiver Information (Vendor) -->
        <div class="section">
            <div class="section-title">ALICI BİLGİLERİ (SATICI)</div>
            <div class="info-row">
                <span class="info-label">Firma:</span>
                <span>{{ $vendor->business_name ?? $vendor->name ?? 'SportoOnline' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Telefon:</span>
                <span>{{ $vendor->phone ?? 'Belirtilmemiş' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Adres:</span>
                <span>{{ $vendor->address ?? 'Merkez Depo' }}</span>
            </div>
        </div>

        <!-- Return Information -->
        <div class="section">
            <div class="section-title">İADE BİLGİLERİ</div>
            <div class="info-row">
                <span class="info-label">İade No:</span>
                <span>{{ $returnRequest->return_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Sipariş No:</span>
                <span>#{{ $order->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">İade Tutarı:</span>
                <span>₺{{ number_format($returnRequest->refund_amount, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">İade Nedeni:</span>
                <span>{{ $returnRequest->reason_category }}</span>
            </div>
        </div>

        <!-- QR Code -->
        <div class="qr-code">
            <img src="{{ $qrCode }}" alt="QR Code" width="150" height="150">
            <p style="margin-top: 10px; font-size: 10px;">QR kodu ile iade takibi yapabilirsiniz</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Bu etiket {{ now()->format('d.m.Y H:i') }} tarihinde oluşturulmuştur.</p>
            <p>Kargo teslimi sırasında bu etiketi paketin üzerine yapıştırınız.</p>
        </div>
    </div>
</body>
</html>
