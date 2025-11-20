@extends('layouts.admin')

@section('title', 'Vendor Erişim İstatistikleri')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">📊 Vendor Bazlı Erişim İstatistikleri</h4>

    <!-- Export Butonu -->
    <button class="btn btn-sm btn-outline-primary mb-3" data-toggle="modal" data-target="#exportModal">
        📁 Logları Export Et
    </button>

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

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.externalPdfLogs.export') }}" method="POST">
                @csrf
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