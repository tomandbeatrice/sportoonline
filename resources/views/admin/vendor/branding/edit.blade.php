@extends('layouts.admin')

@section('content')
<h2>Branding Ayarları</h2>

<form method="POST" action="{{ route('admin.vendor.branding.update', $vendor->id) }}">
    @csrf
    @method('PUT')

    <label>Logo URL</label>
    <input type="text" name="logo_url" value="{{ old('logo_url', $vendor->logo_url) }}">

    <label>Primary Color</label>
    <input type="color" name="primary_color" value="{{ old('primary_color', $vendor->primary_color) }}">

    <label>Secondary Color</label>
    <input type="color" name="secondary_color" value="{{ old('secondary_color', $vendor->secondary_color) }}">

    <button type="submit">Kaydet</button>
</form>
@endsection