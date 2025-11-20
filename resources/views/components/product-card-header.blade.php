@props(['product'])

<h5 class="card-title">{{ $product->name }}</h5>

@if($product->brand)
    <small class="text-muted">{{ $product->brand }}</small>
@endif