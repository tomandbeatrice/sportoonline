@extends('layouts.admin')

@section('title', 'Vendor PDF Özeti')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">📄 Vendor PDF Özeti</h4>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="badge bg-primary">Vendor ID: {{ $vendorId }}</span>
        </div>
        <div>
            <a href="{{ route('admin.vendor.pdf.summary.export', ['vendor' => $vendorId]) }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel"></i> Excel'e Aktar
            </a>
        </div>
    </div>
    

    <form method="GET" class="row g-2 mb-3">
    <input type="hidden" name="vendor" value="{{ $vendorId }}">

    <div class="col-md-3">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Dosya adı ara">
    </div>
    <div class="col-md-2">
        <input type="number" step="0.1" name="min_size" value="{{ request('min_size') }}" class="form-control" placeholder="Min MB">
    </div>
    <div class="col-md-2">
        <input type="number" step="0.1" name="max_size" value="{{ request('max_size') }}" class="form-control" placeholder="Max MB">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-outline-primary w-100">Filtrele</button>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.vendor.pdf.summary', ['vendor' => $vendorId]) }}" class="btn btn-outline-secondary w-100">Temizle</a>
    </div>
</form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Dosya Adı</th>
                    <th>Boyut (MB)</th>
                    <th>İçerik Önizleme</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($summary as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['file'] }}</td>
                        <td>{{ number_format($item['size_mb'], 2) }}</td>
                        <td>{{ $item['preview'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Veri bulunamadı.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $summary->links() }}
        </div>
    </div>
</div>
@endsection