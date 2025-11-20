@props(['product'])

<h5 class="card-title">{{ $product->name }}</h5>

<p class="card-text">
    {{ $product->description ?? 'Açıklama mevcut değil.' }}
</p>

@if($product->stock > 0)
    <span class="badge bg-success">Stokta: {{ $product->stock }}</span>
@else
    <span class="badge bg-danger">Stok yok</span>
@endif