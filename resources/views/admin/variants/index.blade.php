@extends('layouts.admin') {{-- Layout dosyan varsa buna bağlıyoruz --}}
@section('title', 'Varyant Listesi')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">🧬 Varyantlar</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ürün</th>
                <th>Varyant Adı</th>
                <th>Stok</th>
                <th>Fiyat</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($variants as $variant)
                <tr>
                    <td>{{ $variant->id }}</td>
                    <td>{{ $variant->product->name ?? '-' }}</td>
                    <td>{{ $variant->name }}</td>
                    <td>{{ $variant->stock }}</td>
                    <td>{{ number_format($variant->price, 2) }} ₺</td>
                    <td>
                        <a href="{{ route('admin.variants.edit', $variant->id) }}" class="btn btn-sm btn-primary">Düzenle</a>
                        <form action="{{ route('admin.variants.destroy', $variant->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Silmek istediğinize emin misiniz?')" class="btn btn-sm btn-danger">Sil</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Kayıt bulunamadı.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection