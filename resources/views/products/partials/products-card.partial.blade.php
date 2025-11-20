<h5 class="card-title d-flex justify-content-between">
    {{ $product->name }}
    @if($product->variants->first())
        <x-status-badge :status="$product->variants->first()->status" />
    @endif
</h5>

<p class="card-text">{{ Str::limit($product->description, 100) }}</p>
<span class="fw-bold text-success">{{ number_format($product->price, 2) }} ₺</span>