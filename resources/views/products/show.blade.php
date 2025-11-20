@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container mx-auto py-6">

        {{-- Alert Mesajları --}}
        @includeIf('partials.alerts')

        {{-- Ürün Başlığı --}}
        <h1 class="text-2xl font-bold mb-4">{{ $product->name }}</h1>

        {{-- Ürün Görseli --}}
        <img src="{{ $product->images->first()->url ?? 'https://via.placeholder.com/600x400' }}"
             alt="{{ $product->name }}"
             class="rounded mb-6 w-full max-w-xl">

        {{-- Fiyat & Kategori --}}
        <p class="text-lg mb-2">💰 <strong>{{ number_format($product->price, 2) }}₺</strong></p>
        @if($product->category)
            <p class="text-sm text-gray-600">Kategori: {{ $product->category->name }}</p>
        @endif

        {{-- Varyasyonlar --}}
        <h2 class="text-md font-semibold mt-6 mb-2">🎯 Varyasyonlar</h2>
        @includeIf('products.partials.variants', ['variants' => $product->variants])

        {{-- Varyasyon Ekleme --}}
        <h2 class="text-md font-semibold mt-6 mb-2">➕ Yeni Varyasyon Ekle</h2>
        @includeIf('partials.forms.variant-form', ['product' => $product])

        {{-- Yorumlar --}}
        <h2 class="text-md font-semibold mt-6 mb-2">💬 Yorumlar</h2>
        @includeIf('products.partials.comments', ['comments' => $product->comments])

        {{-- Sipariş Bilgisi --}}
        <h2 class="text-md font-semibold mt-6 mb-2">📦 Sipariş Geçmişi</h2>
        @includeIf('products.partials.order_log', ['orders' => $product->orderDetails])
    </div>
@endsection