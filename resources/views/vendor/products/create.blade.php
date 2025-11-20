<form method="POST" action="{{ route('vendor.products.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="text" name="name" placeholder="Ürün adı" required>
    <textarea name="description" placeholder="Açıklama"></textarea>
    <input type="number" name="price" placeholder="Fiyat" required>
    <input type="number" name="stock" placeholder="Stok" required>
    <input type="file" name="image">
    <input type="hidden" name="vendor_id" value="{{ auth()->user()->vendor_id }}">
    <button type="submit">Ürünü Ekle</button>
</form>