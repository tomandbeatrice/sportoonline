@'
@if($product->variants->count())
    <h5 class="mt-4">Varyasyonlar</h5>
    <div class="row row-cols-1 row-cols-md-3 g-3">
        @foreach($product->variants as $variant)
            <div class="col">
                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="card-title">{{ $variant->name }}</h6>
                        <p class="card-text">Fiyat: ₺{{ number_format($variant->price, 2) }}</p>
                        <a href="{{ route('variants.edit', $variant->id) }}" class="btn btn-sm btn-outline-primary">🛠️ Düzenle</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-muted">Bu ürün için varyasyon eklenmemiş.</p>
@endif
'@ | Set-Content -Path ".\variant-list.blade.php" -Encoding UTF8

