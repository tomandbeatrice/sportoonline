<form method="POST" action="{{ route('variants.store') }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">

    <div class="mb-3">
        <label for="variantName" class="form-label">Varyasyon Adı</label>
        <input type="text" class="form-control" id="variantName" name="name" required>
    </div>

    <div class="mb-3">
        <label for="variantPrice" class="form-label">Fiyat (₺)</label>
        <input type="number" class="form-control" id="variantPrice" name="price" step="0.01" required>
    </div>

    <button type="submit" class="btn btn-success">➕ Varyasyon Ekle</button>
</form>