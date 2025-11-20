<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Vendor Export Raporu</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 20px; }
        .branding { margin-top: 20px; }
        h1 { font-size: 20px; }
        p { font-size: 14px; }
    </style>
</head>
<body style="color: {{ $branding['color'] }}">
    <img src="{{ public_path($branding['logo']) }}" width="100" alt="{{ $branding['name'] }} Logo">
    <h1>{{ $branding['name'] }} - Export Raporu</h1>

    <table>
        <thead>
            <tr>
                @foreach($branding['columns'] as $col)
                    <th>{{ ucfirst($col) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    @foreach($branding['columns'] as $col)
                        <td>{{ $item[$col] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top:40px; font-size:12px;">{{ $branding['footer'] }}</p>
</body>
<body>
    <div class="header">
        @include("vendor_branding.{$vendor->slug}")
        <h1>Export Raporu - {{ $vendor->name }}</h1>
    </div>

    <div class="branding">
        <p><strong>Vendor Kodu:</strong> {{ $vendor->code }}</p>
        <p><strong>Export Tarihi:</strong> {{ now()->format('d.m.Y H:i') }}</p>
        <p><strong>Yetkili:</strong> {{ $vendor->responsible_person ?? 'Tanımlı değil' }}</p>
    </div>

    <hr>

    <p>Bu rapor sistem üzerinden oluşturulmuştur. Token doğrulama başarılıysa içerik geçerlidir.</p>
</body>
</html>