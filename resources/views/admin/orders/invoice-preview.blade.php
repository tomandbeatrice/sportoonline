@php
    $vendor = $order->vendor;
    $color = $vendor->theme_color ?? '#555';
    $logo = $vendor->logo_path ? public_path($vendor->logo_path) : public_path('/default-logo.png');
    $watermark = $vendor->watermark_path ? public_path($vendor->watermark_path) : null;
@endphp

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Fatura #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 50px;
            color: #222;
            background: #fff;
            position: relative;
        }

        header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid {{ $color }};
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .logo {
            height: 60px;
            margin-right: 20px;
        }

        h1 {
            font-size: 24px;
            color: {{ $color }};
            margin: 0;
        }

        .watermark {
            position: absolute;
            top: 150px;
            left: 180px;
            width: 300px;
            opacity: 0.06;
            z-index: 0;
        }

        .content {
            z-index: 1;
            position: relative;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 5px 0;
        }

        .highlight {
            background: {{ $color }}22;
            padding: 15px;
            border-radius: 6px;
        }
    </style>
</head>
<body>

    @if($watermark)
        <img src="{{ $watermark }}" class="watermark" alt="Watermark">
    @endif

    <header>
        <img src="{{ $logo }}" class="logo" alt="Vendor Logo">
        <h1>Fatura #{{ $order->id }}</h1>
    </header>

    <div class="content">

        <div class="info">
            <p><strong>Tarih:</strong> {{ $order->created_at->format('d M Y') }}</p>
            <p><strong>Satıcı:</strong> {{ $vendor->name }}</p>
            <p><strong>Alıcı:</strong> {{ $order->user->name }}</p>
        </div>

        <div class="highlight">
            <p><strong>Ürün:</strong> {{ $order->product->name }}</p>
            <p><strong>Adet:</strong> {{ $order->quantity }}</p>
            <p><strong>Tutar:</strong> ₺{{ number_format($order->total_amount, 2, ',', '.') }}</p>
        </div>

    </div>

</body>
</html>