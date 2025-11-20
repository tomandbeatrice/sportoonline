@props(['product'])

<div class="border rounded shadow-sm hover:shadow-md transition p-4 flex flex-col justify-between">
    {{-- Ürün görseli --}}
    <img src="{{ $product->images->first()->url ?? 'https://via.placeholder.com/300x200' }}"
         alt="{{ $product->name }}"
         class="w-full h-48 object-cover mb-4 rounded">

    {{-- Ürün adı --}}
    <h2 class="text-lg font-semibold">{{ $product->name }}</h2>

    {{-- Fiyat --}}
    <p class="text-blue-600 font-bold mt-2">{{ number_format($product->price, 2) }}₺</p>

    {{-- Kategori --}}
    <p class="text-sm text-gray-500">Kategori: {{ $product->category->name ?? '-' }}</p>

    {{-- Varyant sayısı + tooltip --}}
    @if($product->variants->count())
        <p class="text-xs text-gray-600 mt-1">
            <span title="Toplam {{ $product->variants->count() }} varyant tanımlı">
                💠 {{ $product->variants->count() }} varyant
            </span>
        </p>
    @endif

    {{-- Stok durumu --}}
    @php
        $stok = $product->variants->sum('stock');
    @endphp
    <p class="text-sm mt-2">
        <span class="px-2 py-1 rounded 
            {{ $stok > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $stok > 0 ? 'Stokta var' : 'Stokta yok' }}
        </span>
    </p>
</div>