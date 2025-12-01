<form method="POST" action="{{ route('vendor.products.destroy', $product) }}">
    @csrf
    @method('DELETE')
    <button type="submit" onclick="return confirm('Silmek istediğine emin misin?')">Sil</button>
</form>