@extends('layouts.admin')

@section('title', 'Siparişe Not Ekle')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">📝 Sipariş #{{ $order->id }} için Not Ekle</h1>

    <form action="{{ route('order.log.note', $order->id) }}" method="POST">
        @csrf
        <input type="hidden" name="action_type" value="custom_note">

        <div class="mb-3">
            <label for="note" class="form-label">İşlem Açıklaması:</label>
            <textarea name="note" id="note" rows="4" class="form-control" placeholder="Bu işlemle ilgili açıklama girin..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Notu Kaydet</button>
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary ms-2">Geri Dön</a>
    </form>
</div>
@endsection