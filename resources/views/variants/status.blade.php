<form action="{{ route('variants.updateStatus', $variant->id) }}" method="POST">
    @csrf
    <select name="status" onchange="this.form.submit()" class="form-select">
        <option value="active" {{ $variant->status == 'active' ? 'selected' : '' }}>Aktif</option>
        <option value="passive" {{ $variant->status == 'passive' ? 'selected' : '' }}>Pasif</option>
    </select>
</form>

@if(session('success'))
    <x-alert type="success" :message="session('success')" />
@endif
@if($errors->any())
    <x-alert type="error" :message="$errors->first()" />
@endif