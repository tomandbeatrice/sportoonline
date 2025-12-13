<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>İade Kargo Etiketi - {{ $returnNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; }
        .label-container { border: 2px solid #000; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .section { margin: 15px 0; padding: 10px; border: 1px solid #ccc; }
        .section-title { font-weight: bold; font-size: 14px; margin-bottom: 8px; background-color: #f0f0f0; padding: 5px; }
        .info-row { margin: 5px 0; font-size: 12px; }
        .barcode-section { text-align: center; margin: 20px 0; padding: 15px; background-color: #f9f9f9; }
        .barcode { font-size: 28px; font-weight: bold; letter-spacing: 3px; font-family: 'Courier New', monospace; }
        .tracking-number { font-size: 20px; font-weight: bold; margin-top: 10px; }
        .important-note { background-color: #fff3cd; border: 1px solid #ffc107; padding: 10px; margin: 15px 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table td { padding: 5px; font-size: 12px; }
        .label-left { width: 30%; font-weight: bold; }
    </style>
</head>
<body>
    <div class="label-container">
        <!-- Header -->
        <div class="header">
            <h1>İADE KARGO ETİKETİ</h1>
            <p style="font-size: 12px;">SportOnline Marketplace</p>
        </div>

        <!-- Barcode Section -->
        <div class="barcode-section">
            <div class="barcode">*{{ $returnNumber }}*</div>
            <div class="tracking-number">{{ $trackingNumber }}</div>
            <p style="font-size: 11px; margin-top: 5px;">İade No: {{ $returnNumber }}</p>
        </div>

        <!-- Sender (Customer) Information -->
        <div class="section">
            <div class="section-title">GÖNDERİCİ (MÜŞTERİ) BİLGİLERİ</div>
            <table>
                <tr>
                    <td class="label-left">Ad Soyad:</td>
                    <td>{{ $customerName }}</td>
                </tr>
                <tr>
                    <td class="label-left">Adres:</td>
                    <td>{{ $customerAddress }}</td>
                </tr>
            </table>
        </div>

        <!-- Receiver (Vendor) Information -->
        <div class="section">
            <div class="section-title">ALICI (SATICI) BİLGİLERİ</div>
            <table>
                <tr>
                    <td class="label-left">Firma/Ad:</td>
                    <td>{{ $vendorName }}</td>
                </tr>
                <tr>
                    <td class="label-left">Adres:</td>
                    <td>{{ $vendorAddress }}</td>
                </tr>
            </table>
        </div>

        <!-- Shipment Details -->
        <div class="section">
            <div class="section-title">KARGO BİLGİLERİ</div>
            <table>
                <tr>
                    <td class="label-left">Kargo Firması:</td>
                    <td>{{ $carrier }}</td>
                </tr>
                <tr>
                    <td class="label-left">Tarih:</td>
                    <td>{{ $createdAt }}</td>
                </tr>
                <tr>
                    <td class="label-left">İade Tipi:</td>
                    <td>ÜRÜN İADESİ</td>
                </tr>
            </table>
        </div>

        <!-- Important Notes -->
        <div class="important-note">
            <strong>ÖNEMLİ:</strong> Bu etiket sadece iade gönderileri için geçerlidir. Ürünü orijinal ambalajında gönderin. 
            Kargo takip numaranızı kaydedin. İade sürecini sportoonline.com/returns adresinden takip edebilirsiniz.
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
            <p>Bu kargo etiketi SportOnline tarafından otomatik olarak oluşturulmuştur.</p>
            <p>Müşteri Hizmetleri: destek@sportoonline.com | Tel: 0850 XXX XX XX</p>
        </div>
    </div>
</body>
</html>
