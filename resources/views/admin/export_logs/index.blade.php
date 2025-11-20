@extends('layouts.admin')

@section('title', 'Export Dosyaları')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">📁 Export Dosyaları</h2>

    {{-- 🧹 Cleanup Butonu --}}
    <form method="POST" action="{{ route('admin.export.files.cleanup') }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-outline-danger">
            🧹 Silinmiş Dosyaları Logla
        </button>
    </form>

    {{-- 🔍 Duruma Göre Filtreleme --}}
    <form method="GET" action="{{ route('admin.export.files') }}" class="mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="status" class="form-label fw-bold">Durum:</label>
            </div>
            <div class="col-auto">
                <select name="status" id="status" onchange="this.form.submit()" class="form-select">
                    <option value="">Tüm Durumlar</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Hatalı</option>
                    <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Silindi</option>
                </select>
            </div>
        </div>
    </form>

    {{-- 📋 Export Dosyaları Tablosu --}}
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Dosya</th>
                <th>Tip</th>
                <th>Boyut</th>
                <th>Durum</th>
                <th>Export Zamanı</th>
                <th>İndirme</th>
            </tr>
        </thead>
        <tbody>
            @forelse($files as $file)
                <tr class="{{ $file->status === 'failed' ? 'table-danger' : ($file->status === 'deleted' ? 'table-warning' : '') }}">
                    <td>{{ $file->filename }}</td>
                    <td>{{ $file->filetype }}</td>
                    <td>{{ $file->readable_size }}</td>
                    <td>{{ ucfirst($file->status) }}</td>
                    <td>{{ $file->formatted_exported_at }}</td>
                    <td>
                        @if($file->exists_in_storage && $file->status === 'completed')
                            <a href="{{ $file->download_url }}" class="btn btn-sm btn-success">İndir</a>
                        @else
                            <span class="text-muted">İndirilemez</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Kayıt bulunamadı.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 📄 Sayfalama --}}
    <div class="mt-3">
        {{ $files->withQueryString()->links() }}
    </div>
</div>
@endsection