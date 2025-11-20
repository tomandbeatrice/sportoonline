<form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Arama Alanı --}}
    <input type="text" name="search" value="{{ request('search') }}"
           class="form-input border border-gray-300 rounded px-3 py-2"
           placeholder="Ürün adı veya açıklama">

    {{-- Kategori Seçimi --}}
    <select name="category" class="form-select border border-gray-300 rounded px-3 py-2">
        <option value="">Kategori Seç</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    {{-- Sıralama Seçimi --}}
    <select name="sort" class="form-select border border-gray-300 rounded px-3 py-2">
        <option value="">Sırala</option>
        <option value="price_asc" @selected(request('sort') === 'price_asc')>Fiyat (Artan)</option>
        <option value="price_desc" @selected(request('sort') === 'price_desc')>Fiyat (Azalan)</option>
        <option value="newest" @selected(request('sort') === 'newest')>En Yeni</option>
    </select>

    {{-- Filtre Butonu --}}
    <button type="submit"
            class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
        Filtrele
    </button>
</form>