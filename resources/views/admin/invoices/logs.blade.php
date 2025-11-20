@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Export Geçmişi</h2>

    <!-- Filtreleme -->
    <form method="GET" action="{{ route('admin.invoices.logs') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <select name="vendor_id" class="form-control">
                    <option value="">Tüm Vendorlar</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filtrele</button>
            </div>
        </div>
    </form>

    <!-- Log Tablosu -->
    <div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vendor</th>
                <th>Export Tarihi</th>
                <th>Fatura Sayısı</th>
                <th>Boyut</th>
                <th>Tip</th>
                <th>Admin</th>
                <th>Süre</th>
                <th>Durum</th>
                <th>IP</th>
                <th>Event</th>
                <th>Hata</th>
                <th>İndirme</th>
                <th>Detay</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->uuid ?? '-' }}</td>
                    <td>{{ $log->vendor->name ?? '-' }}</td>
                    <td>{{ $log->created_at->format('d.m.Y H:i') }}</td>
                    <td>{{ $log->invoice_count }}</td>
                    <td>{{ $log->size_kb ? number_format($log->size_kb, 0, ',', '.') . ' KB' : '-' }}</td>
                    <td>
                        @if($log->export_type === 'bulk')
                            <span title="Toplu Export" class="text-primary"><i class="fas fa-layer-group"></i></span>
                        @else
                            <span title="Tekli Export" class="text-secondary"><i class="fas fa-file-pdf"></i></span>
                        @endif
                    </td>
                    <td>{{ $log->admin->name ?? '-' }}</td>
                    <td>{{ $log->duration_sec ? $log->duration_sec . ' sn' : '-' }}</td>
                    <td>
                        @if($log->status === 'success')
                            <span class="badge badge-success">Başarılı</span>
                        @elseif($log->status === 'empty')
                            <span class="badge badge-warning">Boş</span>
                        @else
                            <span class="badge badge-secondary">Bilinmiyor</span>
                        @endif
                    </td>
                    <td>{{ $log->ip_address ?? '-' }}</td>
                    <td>
                        @if($log->dispatch_event)
                            <span class="badge badge-success" title="Event gönderildi">✔</span>
                        @else
                            <span class="badge badge-secondary" title="Event yok">✖</span>
                        @endif
                    </td>
                    <td>{{ $log->error_message ?? '-' }}</td>
                    <td>
                        @if($log->download_token)
                            <a href="{{ route('admin.invoices.download.token', $log->download_token) }}" class="btn btn-sm btn-success">İndir</a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#logModal{{ $log->id }}">
                            <i class="fas fa-search"></i>
                        </button>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" role="dialog">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Export Detayı</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                      </div>
                      <div class="modal-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>ID:</strong> {{ $log->uuid ?? '-' }}</li>
                            <li class="list-group-item"><strong>Vendor:</strong> {{ $log->vendor->name ?? '-' }}</li>
                            <li class="list-group-item"><strong>Admin:</strong> {{ $log->admin->name ?? '-' }}</li>
                            <li class="list-group-item"><strong>Export Tarihi:</strong> {{ $log->created_at->format('d.m.Y H:i') }}</li>
                            <li class="list-group-item"><strong>Fatura Sayısı:</strong> {{ $log->invoice_count }}</li>
                            <li class="list-group-item"><strong>Boyut:</strong> {{ $log->size_kb ? number_format($log->size_kb, 0, ',', '.') . ' KB' : '-' }}</li>
                            <li class="list-group-item"><strong>Tip:</strong> {{ $log->export_type === 'bulk' ? 'Toplu' : 'Tekli' }}</li>
                            <li class="list-group-item"><strong>Süre:</strong> {{ $log->duration_sec ? $log->duration_sec . ' sn' : '-' }}</li>
                            <li class="list-group-item"><strong>Durum:</strong> {{ $log->status ?? '-' }}</li>
                            <li class="list-group-item"><strong>IP:</strong> {{ $log->ip_address ?? '-' }}</li>
                            <li class="list-group-item"><strong>Event:</strong> {{ $log->dispatch_event ? '✓' : '✖' }}</li>
                            <li class="list-group-item"><strong>Hata Mesajı:</strong> {{ $log->error_message ?? '-' }}</li>
                            <li class="list-group-item"><strong>Token:</strong> {{ $log->download_token ?? '-' }}</li>
                            <li class="list-group-item"><strong>Dosya Yolu:</strong> {{ $log->path }}</li>
                        </ul>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                      </div>
                    </div>
                  </div>
                </div>
            @endforeach
        </tbody>
    </table>
    </div>

    {{ $logs->links() }}
</div>
@endsection