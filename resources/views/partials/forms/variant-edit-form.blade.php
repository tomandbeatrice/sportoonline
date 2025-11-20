<form method="POST" action="{{ route('admin.variants.update', $variant->id) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="variantTitle" class="form-label">Başlık</label>
        <input type="text" class="form-control" id="variantTitle" name="title" value="{{ old('title', $variant->title) }}" required>
    </div>

    <div class="mb-3">
        <label for="variantPrice" class="form-label">Fiyat (₺)</label>
        <input type="number" class="form-control" id="variantPrice" name="price" value="{{ old('price', $variant->price) }}" step="0.01" required>
    </div>

    <div class="mb-3">
        <label for="variantStock" class="form-label">Stok</label>
        <input type="number" class="form-control" id="variantStock" name="stock" value="{{ old('stock', $variant->stock) }}" required>
    </div>

    <div class="mb-3">
        <label for="variantProduct" class="form-label">Ürün</label>
        <select class="form-select" name="product_id" id="variantProduct" required>
            @foreach($products as $product)
                <option value="{{ $product->id }}" {{ $product->id == old('product_id', $variant->product_id) ? 'selected' : '' }}>
                    {{ $product->title }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">💾 Güncelle</button>
</form>