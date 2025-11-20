<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Fatura - {{ $vendor }}</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2>{{ $vendor }} Fatura Özeti</h2>
    <table>
        <thead>
            <tr><th>Ürün</th><th>Miktar</th><th>Fiyat</th></tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $item['price'] }} ₺</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>