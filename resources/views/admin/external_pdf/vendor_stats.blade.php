@extends('layouts.admin')

@section('title', 'Vendor Erişim İstatistikleri')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">📊 Vendor Bazlı Erişim İstatistikleri</h4>

    <!-- 🔍 Filtre Formu -->
    <form method="GET" action="{{ route('admin.externalPdf.vendorStats') }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <select name="vendor_id" class="form-control">
                    <option value="">Tüm Vendorlar</option>
                    @foreach($vendorList as $vendor)
                        <option value="{{ $vendor->id }}" {{ $selectedVendorId == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filtrele</button>
            </div>
        </div>
    </form>

    <!-- 📁 Export Butonu -->
    <button class="btn btn-sm btn-outline-primary mb-3" data-toggle="modal" data-target="#exportModal">
        Logları Export Et
    </button>

    <!-- 📊 İstatistik Tablosu -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Vendor</th>
                    <th>Token</th>
                    <th>Durum</th>
                    <th>Toplam Erişim</th>
                    <th>Son Erişim</th>
                    <th>En Çok Erişilen Dosya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $v)
                    <tr>
                        <td>{{ $v->name }}</td>
                        <td>{{ $v->token }}</td>
                        <td>{{ $v->active ? 'Aktif' : 'Pasif' }}</td>
                        <td>{{ $v->access_count }}</td>
                        <td>{{ $v->last_access ? \Carbon\Carbon::parse($v->last_access)->format('d.m.Y H:i') : 'Yok' }}</td>
                        <td>{{ $v->most_accessed_file ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Vendor bulunamadı</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- 📤 Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.externalPdfLogs.export') }}" method="POST">
                @csrf
                <input type="hidden" name="vendor_id" value="{{ $selectedVendorId }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <div class="modal-header">
                    <h5 class="modal-title">Export Ayarları</h5>
                </div>
                <div class="modal-body">
                    <label>Format:</label>
                    <select name="format" class="form-control">
                        <option value="xlsx">Excel</option>
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF (Önizleme)</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Export Et</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection