@extends('admin.layouts.app')

@section('title', 'Export Log Geçmişi')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">📊 Export Aktivite Logları</h1>

    {{-- 📈 Export Summary Panel --}}
    @if($logs->count() > 0)
    @php
        $vendorCount = $logs->pluck('vendor_name')->unique()->count();
        $topFile = $logs->groupBy('file_name')->sortByDesc(fn($group) => $group->count())->keys()->first();
    @endphp
    <div class="card mb-4 border-success">
        <div class="card-header bg-success text-white">
            Export Özet Paneli
        </div>
        <div class="card-body row text-center">
            <div class="col-md-4">
                <h5>{{ $logs->total() }}</h5>
                <p>Toplam Export Logu</p>
            </div>
            <div class="col-md-4">
                <h5>{{ $vendorCount }}</h5>
                <p>Eşsiz Vendor Sayısı</p>
            </div>
            <div class="col-md-4">
                <h5>{{ $topFile }}</h5>
                <p>En Sık Exportlanan Dosya</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ✅ Filtre Formu --}}
    <form method="GET" action="{{ route('admin.logs.exportActivity') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <select name="vendor" class="form-select">
                    <option value="">Tüm Vendorlar</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @selected(request('vendor') == $vendor->id)>{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filtrele</button>
            </div>
        </div>
    </form>

    {{-- 🔔 Alert Mesajı --}}
    @if($logs->isEmpty())
        <div class="alert alert-warning">Hiç export logu bulunamadı. Filtreleri kontrol et!</div>
    @endif

    {{-- 📋 Log Tablosu --}}
    @if($logs->isNotEmpty())
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Vendor</th>
                    <th>Admin</th>
                    <th>Email</th>
                    <th>Dosya Adı</th>
                    <th>İşlem Türü</th>
                    <th>IP</th>
                    <th>Tarih</th>
                    <th>Detay</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $loop->iteration + $logs->firstItem() - 1 }}</td>
                    <td>{{ $log->vendor_name }}</td>
                    <td>{{ $log->admin_name }}</td>
                    <td>{{ $log->user_email }}</td>
                    <td>{{ $log->file_name }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->ip }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d.m.Y H:i') }}</td>
                    <td>
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                            Detay
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 📌 Pagination --}}
    <div class="mt-3">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif

    {{-- 🧾 Modal Detayları --}}
    @foreach($logs as $log)
    <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-labelledby="logModalLabel{{ $log->id }}" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="logModalLabel{{ $log->id }}">Export Log Detayı</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
          </div>
          <div class="modal-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item"><strong>ID:</strong> {{ $log->id }}</li>
              <li class="list-group-item"><strong>Vendor:</strong> {{ $log->vendor_name }}</li>
              <li class="list-group-item"><strong>Admin:</strong> {{ $log->admin_name }}</li>
              <li class="list-group-item"><strong>Email:</strong> {{ $log->user_email }}</li>
              <li class="list-group-item"><strong>Dosya Adı:</strong> {{ $log->file_name }}</li>
              <li class="list-group-item"><strong>İşlem Türü:</strong> {{ $log->action }}</li>
              <li class="list-group-item"><strong>IP:</strong> {{ $log->ip }}</li>
              <li class="list-group-item"><strong>Tarih:</strong> {{ \Carbon\Carbon::parse($log->created_at)->format('d.m.Y H:i') }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    @endforeach
</div>
@endsection