<form method="POST" action="{{ route('vendor.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <input type="text" name="name" value="{{ $product->name }}" required>
    <textarea name="description">{{ $product->description }}</textarea>
    <input type="number" name="price" value="{{ $product->price }}" required>
    <input type="number" name="stock" value="{{ $product->stock }}" required>
    <input type="file" name="image">
    <button type="submit">Güncelle</button>
</form>