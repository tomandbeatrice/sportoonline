@extends('layouts.admin')

@section('title', 'Vendor PDF Özeti')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">📄 Vendor PDF Özeti</h4>

    {{-- 🔍 Filtre Formu --}}
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

    {{-- 🔗 Export & Vendor Bilgisi --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-primary">Vendor ID: {{ $vendorId }}</span>
        <a href="{{ route('admin.vendor.pdf.summary.export', ['vendor' => $vendorId]) }}" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel"></i> Excel'e Aktar
        </a>
    </div>

    {{-- 📄 Dosya Bazlı Özet --}}
    <div class="table-responsive mb-5">
        <h5 class="mb-3">📁 Dosya Bazlı PDF Özeti</h5>
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Dosya Adı</th>
                    <th>Boyut (MB)</th>
                    <th>İçerik Önizleme</th>
                    <th>Modal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($summary['files'] as $index => $item)
                    @php
                        $token = app(\App\Services\VendorPdfPreviewService::class)->generateToken($vendorId, $item['file']);
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['file'] }}</td>
                        <td>{{ number_format($item['size_mb'], 2) }}</td>
                        <td>{{ $item['preview'] }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#pdfModal{{ $index }}">
                                Önizle
                            </button>

                            <div class="modal fade" id="pdfModal{{ $index }}" tabindex="-1">
                              <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">PDF Önizleme: {{ $item['file'] }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>
                                  <div class="modal-body">
                                    <iframe src="{{ route('admin.vendor.pdf.preview', ['token' => $token]) }}"
                                            width="100%" height="600px" style="border:none;"></iframe>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Veri bulunamadı.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- 📌 Pagination --}}
        <div class="mt-3">
            {{ $summary['files']->links() }}
        </div>
    </div>

    {{-- 🧠 Başlık Frekansı Özeti --}}
    <div class="table-responsive">
        <h5 class="mb-3">🧠 PDF Başlık Frekansı</h5>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Başlık</th>
                    <th>Frekans</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($summary['titles'] as $title => $count)
                    <tr>
                        <td>{{ $title }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-muted text-center">Veri bulunamadı.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection