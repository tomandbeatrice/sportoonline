{{-- resources/views/components/status-form.blade.php --}}
@props(['variant'])

<form action="{{ route('variants.updateStatus', $variant->id) }}" method="POST" class="d-inline-block">
    @csrf
    @method('PATCH')
    <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
        <option value="aktif" {{ $variant->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="pasif" {{ $variant->status === 'pasif' ? 'selected' : '' }}>Pasif</option>
        <option value="taslak" {{ $variant->status === 'taslak' ? 'selected' : '' }}>Taslak</option>
    </select>
</form>