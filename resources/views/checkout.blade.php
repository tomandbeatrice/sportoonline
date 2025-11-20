@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Siparişi Tamamla</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('checkout.process') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Ad Soyad:</label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Adres:</label>
            <textarea name="address" class="form-control" id="address" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Ödeme Metodu:</label>
            <select name="payment_method" class="form-select" id="payment_method" required>
                <option value="credit_card">Kredi Kartı</option>
                <option value="iyzico">iyzico (dummy)</option>
                <option value="bank_transfer">Havale/EFT</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">Sipariş Notu:</label>
            <textarea name="note" class="form-control" id="note" rows="2"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Siparişi Gönder</button>
    </form>
</div>
@endsection