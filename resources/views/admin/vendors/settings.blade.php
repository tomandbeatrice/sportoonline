@extends('layouts.admin')

@section('title', 'Vendor Ayarları')

@section('content')
<div class="container">
    <h2 class="mb-4">Vendor Ayarları – {{ $vendor->name }}</h2>

    <form action="{{ route('admin.vendor.settings.save', $vendor) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="logo" class="form-label">Logo Yükle</label>
            <input type="file" name="branding[logo]" id="logo" class="form-control">
            @if(!empty($vendor->branding['logo']))
                <img src="{{ asset($vendor->branding['logo']) }}" alt="Logo" class="mt-2" style="max-height: 80px;">
            @endif
        </div>

        <div class="mb-3">
            <label for="primaryColor" class="form-label">Ana Renk</label>
            <input type="text" name="branding[colors][primary]" id="primaryColor" class="form-control" value="{{ $vendor->branding['colors']['primary'] ?? '' }}">
        </div>
  <div class="mb-3">
    <label for="token" class="form-label">PDF Erişim Token</label>
    <input type="text" name="external_pdf_token" id="token" class="form-control"
        value="{{ $vendor->external_pdf_token ?? '' }}">
    <small class="form-text text-muted">
        Bu token ile dış erişim sağlanır: 
        <code>{{ url('/public/vendor-pdf/' . $vendor->external_pdf_token) }}</code>
    </small>
</div>

        <div class="mb-3">
            <label for="columns" class="form-label">PDF Kolonları (virgülle ayır)</label>
            <input type="text" name="branding[columns]" id="columns" class="form-control" placeholder="name,price,category" value="{{ implode(',', $vendor->branding['columns'] ?? ['name','price','category']) }}">
        </div>

        <button type="submit" class="btn btn-primary">Kaydet</button>
        <a href="{{ route('admin.vendor.settings.test', $vendor) }}?columns={{ implode(',', $vendor->branding['columns'] ?? ['name','price','category']) }}" target="_blank" class="btn btn-secondary ms-2">📄 PDF Test</a>
    </form>
</div>
@endsection