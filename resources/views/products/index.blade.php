@extends('layouts.app')

@section('title', 'Ürünler')

@section('content')
    <div class="container mx-auto py-6">
        <h1 class="text-2xl font-bold mb-4">Tüm Ürünler</h1>

        {{-- Filtre / Arama Modülü (Varsa) --}}
        @includeIf('products.partials.filter')

        {{-- Ürün Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-3 text-center text-gray-500 py-12">
                    Henüz ürün eklenmemiş.
                </div>
            @endforelse
        </div>

        {{-- Sayfalama --}}
        @if($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection