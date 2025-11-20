<h2>Ürünlerim</h2>

<table>
    <thead>
        <tr>
            <th>Ad</th>
            <th>Fiyat</th>
            <th>Stok</th>
            <th>Görsel</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ number_format($product->price, 2) }} ₺</td>
            <td>{{ $product->stock }}</td>
            <td>
                @if ($product->image)
                    <img src="{{ $product->image_url }}" width="80">
                @else
                    Yok
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>