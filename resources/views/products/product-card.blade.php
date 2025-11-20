<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition p-4 flex flex-col h-full relative">

    {{-- Admin Panel Aksiyonları (Opsiyonel) --}}
    @auth
        @if(auth()->user()->isAdmin())
            <div class="absolute top-2 right-2 flex space-x-2">
                <a href="{{ route('products.edit', $product->id) }}"
                   class="text-sm text-blue-500 hover:text-blue-700" title="Düzenle">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                      onsubmit="return confirm('Silmek istediğine emin misin?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700" title="Sil">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        @endif
    @endauth

    {{-- Ürün Görseli --}}
    @php $image = $product->images[0] ?? null; @endphp
    @if($image)
        <img src="{{ asset('storage/' . $image) }}"
             alt="{{ $product->title }}"
             class="w-full h-48 object-cover rounded">
    @else
        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">
            Görsel yok
        </div>
    @endif

    {{-- Ürün Bilgileri --}}
    <div class="mt-4 flex-1 flex flex-col justify-between">
        <div>
            {{-- Başlık --}}
            <h3 class="text-lg font-semibold text-gray-800">
                {{ $product->title }}
            </h3>

            {{-- Açıklama --}}
            <p class="text-sm text-gray-600 line-clamp-2">
                {{ $product->description }}
            </p>
        </div>

        {{-- Fiyat & Kategori --}}
        <div class="mt-2">
            <span class="text-green-600 font-bold text-lg">
                ₺{{ number_format($product->price, 2, ',', '.') }}
            </span>
            <span class="ml-2 text-xs text-gray-400">
                {{ $product->category->name ?? 'Kategori yok' }}
            </span>
        </div>

        {{-- Varyantlar (Varsa) --}}
        @if(isset($product->variants) && count($product->variants))
            <div class="mt-2 flex flex-wrap gap-1 text-sm text-gray-500">
                @foreach($product->variants as $variant)
                    <span class="px-2 py-1 bg-gray-100 rounded">
                        {{ $variant->name }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Stok Bilgisi --}}
        <div class="mt-2 text-sm text-gray-500">
            Stok: {{ $product->stock > 0 ? $product->stock : 'Stokta yok' }}
        </div>

        {{-- Butonlar --}}
        <div class="mt-4 grid grid-cols-1 gap-2">
            {{-- Detaylar --}}
            <a href="{{ route('products.show', $product->id) }}"
               class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded transition">
                Detayları Gör
            </a>

            {{-- Sepete Ekle (Opsiyonel) --}}
            @if($product->stock > 0)
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 rounded transition">
                        Sepete Ekle
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>