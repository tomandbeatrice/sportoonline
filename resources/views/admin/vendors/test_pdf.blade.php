<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vendor Branding PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #666; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .logo { max-height: 60px; margin-bottom: 10px; }
    </style>
</head>
<body>

    @if(!empty($branding['logo']))
        <img src="{{ public_path($branding['logo']) }}" alt="Logo" class="logo">
    @endif

    <h2>Vendor: {{ $vendor->name }}</h2>

    <table>
        <thead>
            <tr>
                @foreach($columns as $col)
                    <th>{{ ucfirst($col) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    @foreach($columns as $col)
                        <td>{{ $item[$col] ?? '-' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>