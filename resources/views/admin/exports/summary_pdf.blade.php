<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Vendor Export Özeti</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .meta { margin-top: 10px; font-size: 11px; text-align: right; }
    </style>
</head>
<body>
    <h2>📊 Vendor Export Özeti</h2>

    <div class="meta">
        Tarih: {{ \Carbon\Carbon::now()->format('d.m.Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Vendor</th>
                <th>Toplam Export Sayısı</th>
                <th>Son Export Tarihi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary as $row)
                <tr>
                    <td>{{ $row->vendor_name }}</td>
                    <td>{{ $row->total_exports }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->last_export_date)->format('d.m.Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>