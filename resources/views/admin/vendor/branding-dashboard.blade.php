@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">🎨 Vendor Branding Dashboard</h2>

    {{-- Export Branding --}}
    <div class="card mb-4">
        <div class="card-header">📤 Branding Export</div>
        <div class="card-body">
            <a href="{{ route('admin.vendor.branding.export.admin') }}" class="btn btn-success">Excel Olarak Dışa Aktar</a>
        </div>
    </div>

    {{-- Import Branding --}}
    <div class="card mb-4">
        <div class="card-header">📥 Branding Import</div>
        <div class="card-body">
            <form action="{{ route('admin.vendor.branding.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Excel'den Branding Güncelle</button>
            </form>
        </div>
    </div>

    {{-- Export Logları --}}
    <div class="card mb-4">
        <div class="card-header">📑 Son Export Logları</div>
        <div class="card-body">
            <ul>
                @foreach($logs as $log)
                    <li>{{ $log->filename }} - {{ $log->created_at->format('d.m.Y H:i') }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection