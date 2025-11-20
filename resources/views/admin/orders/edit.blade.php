@extends('layouts.admin')

@section('title', 'Sipariş Güncelle')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">✏️ Sipariş #{{ $order->id }} Durum Güncelle</h1>

    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-md-4">
            <label for="status" class="form-label">Durum</label>
            <select name="status" id="status" class="form-select" required>
                <option value="pending" @selected($order->status === 'pending')>Beklemede</option>
                <option value="shipped" @selected($order->status === 'shipped')>Gönderildi</option>
                <option value="cancelled" @selected($order->status === 'cancelled')>İptal Edildi</option>
            </select>
        </div>

        <div class="col-md-12">
            <button type="submit" class="btn btn-success">Güncelle</button>
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary ms-2">Geri Dön</a>
        </div>
    </form>
</div>
@endsection