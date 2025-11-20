@extends('layouts.admin')

@section('content')
<h1 class="mb-4">📦 Siparişler</h1>

@include('admin.partials.alert')

<form method="GET" class="row g-3 mb-4 align-items-end">
    <div class="col-md-3">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Kullanıcı adı veya ID">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Durum Seç</option>
            <option value="pending" @selected(request('status') === 'pending')>Beklemede</option>
            <option value="shipped" @selected(request('status') === 'shipped')>Gönderildi</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>İptal</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
    </div>
    <div class="col-md-2">
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
    </div>
    <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-filter me-1"></i> Filtrele
        </button>
        <a href="{{ route('admin.orders.export') }}" class="btn btn-success w-100">
            <i class="bi bi-download me-1"></i> Export CSV
        </a>
    </div>
</form>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Kullanıcı</th>
            <th>Ürünler</th>
            <th>Toplam</th>
            <th>Durum</th>
            <th>Tarih</th>
            <th>İşlem</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user->name }}</td>
                <td>
                    <ul class="list-unstyled mb-0">
                        @foreach($order->items as $item)
                            <li class="text-truncate">{{ $item->product->name }} x {{ $item->quantity }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>{{ number_format($order->total_price, 2) }}₺</td>
                <td>
                    <span class="badge 
                        @if($order->status === 'pending') bg-warning
                        @elseif($order->status === 'shipped') bg-success
                        @else bg-secondary @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> Detay
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Sipariş bulunamadı.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $orders->withQueryString()->links() }}
@endsection