@extends('layouts.admin')

@section('content')
<h2>👁️ Vendor Branding Önizleme: {{ $vendor->name }}</h2>

@if($vendor->branding)
    {{-- Branding Tema Önizlemesi --}}
    <div style="padding:20px; margin-bottom:30px;
        background-color: {{ $vendor->branding->primary_color }};
        color: {{ $vendor->branding->secondary_color }};
        font-family: {{ $vendor->branding->font_family }};
        border-radius: 8px;">
        <img src="{{ $vendor->branding->logo_url }}" alt="Logo" style="max-width: 200px;"><br><br>
        <h2>{{ $vendor->name }}</h2>
        <p>Primary Color: {{ $vendor->branding->primary_color }}</p>
        <p>Secondary Color: {{ $vendor->branding->secondary_color }}</p>
        <p>Font: {{ $vendor->branding->font_family }}</p>
    </div>

    {{-- Teknik Detay Kutusu --}}
    <div style="border: 1px solid #ccc; padding: 20px;">
        <strong>Logo URL:</strong> {{ $vendor->branding->logo_url }}<br>
        <strong>Primary Color:</strong>
        <span style="background: {{ $vendor->branding->primary_color }}; padding: 10px;">&nbsp;</span><br>
        <strong>Secondary Color:</strong>
        <span style="background: {{ $vendor->branding->secondary_color }}; padding: 10px;">&nbsp;</span><br>
        <strong>Font:</strong> {{ $vendor->branding->font_family }}
    </div>
@else
    <p>Branding bilgisi bulunamadı.</p>
@endif

<a href="{{ route('admin.vendor.branding.index') }}" class="btn btn-secondary mt-4">← Geri Dön</a>
@endsection