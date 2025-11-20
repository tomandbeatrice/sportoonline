{{-- resources/views/components/product-card-footer.blade.php --}}
<div class="flex justify-between items-center px-4 py-2 border-t">
    <div class="text-sm text-gray-500">
        @if($product->updated_at)
            Son güncelleme: {{ $product->updated_at->diffForHumans() }}
        @endif
    </div>

    <div class="flex gap-2">
        <a href="{{ route('products.edit', $product->id) }}"
           class="text-blue-600 hover:text-blue-800 text-sm font-medium">Düzenle</a>

        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
              onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="text-red-600 hover:text-red-800 text-sm font-medium">Sil</button>
        </form>

        {{-- Varyant durum bileşeni --}}
        @php $variant = $product->variants->first(); @endphp
        @if($variant)
            <x-status-form :variant="$variant" />
        @endif
    </div>
</div>