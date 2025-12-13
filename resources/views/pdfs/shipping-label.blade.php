<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kargo Etiketi - {{ $returnRequest->return_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .label-container {
            border: 3px solid #000;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #3B82F6;
            margin-bottom: 5px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }
        .section {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .section-content {
            font-size: 14px;
            font-weight: bold;
        }
        .barcode-container {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .qrcode-container {
            text-align: center;
            margin: 15px 0;
        }
        .return-number {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            background-color: #000;
            color: #fff;
            margin: 15px 0;
            letter-spacing: 3px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin: 10px 0;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            padding: 5px;
            font-size: 11px;
            color: #666;
            width: 35%;
        }
        .info-value {
            display: table-cell;
            padding: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="header">
            <div class="logo">SPORTOONLINE</div>
            <div class="title">İADE KARGO ETİKETİ</div>
        </div>

        <div class="return-number">{{ $returnRequest->return_number }}</div>

        <div class="section">
            <div class="section-title">GÖNDEREN (MÜŞTERİ)</div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Ad Soyad:</div>
                        <div class="info-value">{{ $returnRequest->user->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Telefon:</div>
                        <div class="info-value">{{ $returnRequest->user->phone ?? 'Belirtilmemiş' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">E-posta:</div>
                        <div class="info-value">{{ $returnRequest->user->email }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">ALICI (SATICI/DEPO)</div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Firma:</div>
                        <div class="info-value">{{ $returnRequest->vendor->company_name ?? 'SportOnline' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Adres:</div>
                        <div class="info-value">{{ $returnRequest->vendor->address ?? 'Merkez Depo' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">İADE BİLGİLERİ</div>
            <div class="section-content">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Sipariş No:</div>
                        <div class="info-value">#{{ $returnRequest->order_id }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">İade Tarihi:</div>
                        <div class="info-value">{{ $returnRequest->created_at->format('d.m.Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">İade Nedeni:</div>
                        <div class="info-value">{{ $returnRequest->reason_category }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="barcode-container">
            <div class="section-title">BARCODE</div>
            {!! $barcode !!}
            <div style="margin-top: 5px; font-size: 14px; font-weight: bold;">
                {{ $returnRequest->return_number }}
            </div>
        </div>

        <div class="qrcode-container">
            <div class="section-title">QR CODE</div>
            {!! $qrcode !!}
        </div>

        <div class="footer">
            <p><strong>ÖNEMLİ:</strong> Bu kargo etiketini paketin üzerine yapıştırın.</p>
            <p>Kargo ücreti tarafımızca karşılanmaktadır.</p>
            <p>© {{ date('Y') }} SportOnline - Tüm hakları saklıdır</p>
        </div>
    </div>
</body>
</html>
