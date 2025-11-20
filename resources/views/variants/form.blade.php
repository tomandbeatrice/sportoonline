<div x-data="{ variants: @json(old('variants', $product->variants ?? [])) }" class="space-y-4">

    {{-- Varyant Listesi --}}
    <template x-for="(variant, index) in variants" :key="index">
        <div class="grid grid-cols-12 gap-4 items-center bg-gray-50 p-4 rounded-md">
            {{-- Varyant Adı --}}
            <div class="col-span-5">
                <label class="block text-sm font-medium text-gray-700">Varyant Adı</label>
                <input type="text"
                       :name="`variants[${index}][name]`"
                       x-model="variant.name"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                       required>
            </div>

            {{-- Fiyat --}}
            <div class="col-span-4">
                <label class="block text-sm font-medium text-gray-700">Fiyat (₺)</label>
                <input type="number"
                       step="0.01"
                       min="0"
                       :name="`variants[${index}][price]`"
                       x-model="variant.price"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            {{-- Sil Butonu --}}
            <div class="col-span-3 flex justify-end items-start pt-6">
                <button type="button"
                        class="text-red-600 hover:text-red-800 text-sm"
                        @click="variants.splice(index, 1)">
                    <i class="fas fa-trash-alt"></i> Sil
                </button>
            </div>
        </div>
    </template>

    {{-- Varyant Ekle Butonu --}}
    <div class="flex justify-end">
        <button type="button"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm"
                @click="variants.push({ name: '', price: '' })">
            + Varyant Ekle
        </button>
    </div>
</div>