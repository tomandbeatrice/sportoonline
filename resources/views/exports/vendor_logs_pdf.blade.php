<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><style>table { width: 100%; border-collapse: collapse } th, td { border: 1px solid #ccc; padding: 6px; }</style></head>
<body>
    <h2>Vendor Log Export (PDF)</h2>
    <table>
        <thead>
            <tr>
                <th>Vendor</th>
                <th>Token</th>
                <th>Durum</th>
                <th>Erişim Sayısı</th>
                <th>Son Erişim</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $log)
            <tr>
                <td>{{ $log->vendor_name }}</td>
                <td>{{ $log->token }}</td>
                <td>{{ $log->active ? 'Aktif' : 'Pasif' }}</td>
                <td>{{ $log->access_count }}</td>
                <td>{{ $log->last_access }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>