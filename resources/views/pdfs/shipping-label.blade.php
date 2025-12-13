<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .label { padding: 20px; }
        .barcode { text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="label">
        <h2>Return Shipping Label</h2>
        <p><strong>Return Number:</strong> {{ $returnRequest->return_number }}</p>
        <p><strong>Customer:</strong> {{ $user->name ?? 'N/A' }}</p>
        <p><strong>Address:</strong> {{ $user->address ?? 'Address missing' }}</p>
        <div class="barcode">
            <img src="{{ $barcode }}" alt="Barcode">
        </div>
    </div>
</body>
</html>
