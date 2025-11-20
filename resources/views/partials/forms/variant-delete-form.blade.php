<form method="POST" action="{{ route('variant.destroy', $variant->id) }}" onsubmit="return confirm('Bu varyasyonu silmek istediğinize emin misiniz?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">🗑️ Sil</button>
</form>
