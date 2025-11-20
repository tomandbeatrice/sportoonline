@extends('layouts.admin')
@section('title', 'Admin Logları')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">🛠 {{ $admin->name }} → İşlem Geçmişi</h2>

    @if($logs->isEmpty())
        <p>Henüz işlem bulunamadı.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Sipariş No</th>
                        <th>Tarih</th>
                        <th>Durum Geçişi</th>
                        <th>İşlem Tipi</th>
                        <th>Açıklama</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->order_id }}</td>
                            <td>{{ $log->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ ucfirst($log->old_status) }} → {{ ucfirst($log->new_status) }}</td>
                            <td>
                                @if($log->action_type === 'custom_note')
                                    <span class="badge bg-info"><i class="bi bi-pencil"></i> Not</span>
                                @elseif($log->action_type === 'status_change')
                                    <span class="badge bg-warning"><i class="bi bi-arrow-repeat"></i> Durum</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-tools"></i> Diğer</span>
                                @endif
                            </td>
                            <td>{{ $log->note ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection