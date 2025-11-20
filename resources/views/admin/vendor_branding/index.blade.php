@extends('layouts.admin')

@section('content')
<h2>🎨 Vendor Branding Paneli</h2>

<form method="GET" action="{{ route('admin.vendor.branding.index') }}" class="mb-4">
    <input type="text" name="name" value="{{ request('name') }}" placeholder="Vendor adıyla filtrele">
    <button type="submit">Filtrele</button>
</form>

<a href="{{ route('admin.vendor.branding.export') }}" class="btn btn-success mb-3">Excel Export</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Vendor</th>
            <th>Logo</th>
            <th>Renkler</th>
            <th>Font</th>
            <th>Güncelle</th>
            <th>Önizleme</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vendors as $vendor)
        <tr>
            <td>{{ $vendor->name }}</td>
            <td>
                @if($vendor->branding && $vendor->branding->logo_url)
                    <img src="{{ $vendor->branding->logo_url }}" alt="Logo" width="50">
                @endif
            </td>
            <td>
                <span style="background: {{ $vendor->branding->primary_color ?? '#ccc' }}; padding: 5px;">&nbsp;</span>
                <span style="background: {{ $vendor->branding->secondary_color ?? '#ccc' }}; padding: 5px;">&nbsp;</span>
            </td>
            <td>{{ $vendor->branding->font_family ?? '—' }}</td>
            <td>
                <form method="POST" action="{{ route('admin.vendor.branding.update', $vendor->id) }}">
                    @csrf @method('PUT')
                    <input type="text" name="logo_url" value="{{ $vendor->branding->logo_url ?? '' }}" placeholder="Logo URL">
                    <input type="text" name="primary_color" value="{{ $vendor->branding->primary_color ?? '' }}" placeholder="Primary Color">
                    <input type="text" name="secondary_color" value="{{ $vendor->branding->secondary_color ?? '' }}" placeholder="Secondary Color">
                    <input type="text" name="font_family" value="{{ $vendor->branding->font_family ?? '' }}" placeholder="Font Family">
                    <button type="submit">Güncelle</button>
                </form>
            </td>
            <td>
                <button type="button" onclick="openModal({{ $vendor->id }})">Preview</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $vendors->links() }}

<!-- Modal -->
<div id="brandingModal" style="display:none; position:fixed; top:10%; left:20%; width:60%; background:#fff; padding:20px; border:1px solid #ccc; z-index:999;">
    <div id="brandingContent"></div>
    <button onclick="closeModal()">Kapat</button>
</div>

<script>
function openModal(vendorId) {
    fetch(`/admin/vendor/branding-preview/${vendorId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('brandingContent').innerHTML = html;
            document.getElementById('brandingModal').style.display = 'block';
        });
}
function closeModal() {
    document.getElementById('brandingModal').style.display = 'none';
}
</script>
@endsection