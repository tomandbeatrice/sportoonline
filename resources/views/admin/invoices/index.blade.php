@extends('layouts.admin')

@section('title', 'Export Geçmişi')

@section('content')
<div class="container">
    <h2 class="mb-4">Export Geçmişi</h2>

    {{-- Alert sistemi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Filtreleme formu --}}
    <form method="GET" action="{{ route('admin.exports.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="vendor" class="form-control" placeholder="Vendor" value="{{ request('vendor') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="date" class="form-control" placeholder="Tarih (YYYY-MM-DD)" value="{{ request('date') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filtrele</button>
            </div>
        </div>
    </form>

    {{-- Dosya listesi --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Dosya</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($files as $file)
                <tr>
                    <td>{{ basename($file) }}</td>
                    <td class="d-flex gap-2">
                        {{-- İndirme --}}
                        <form method="GET" action="{{ route('admin.exports.download') }}">
                            <input type="hidden" name="path" value="{{ $file }}">
                            <input type="hidden" name="token" value="{{ \Illuminate\Support\Str::uuid() }}">
                            <button type="submit" class="btn btn-sm btn-success">İndir</button>
                        </form>

                        {{-- Görüntüleme --}}
                        <form method="GET" action="{{ route('admin.exports.view') }}" target="_blank">
                            <input type="hidden" name="path" value="{{ $file }}">
                            <button type="submit" class="btn btn-sm btn-info">Görüntüle</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="2">Kriterlere uyan dosya bulunamadı.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Vendor'a özel export butonu --}}
    @if($vendor)
        <form action="{{ route('admin.exports.export') }}" method="POST" class="mt-4">
            @csrf
            <input type="hidden" name="vendor" value="{{ $vendor }}">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-file-pdf"></i> {{ $vendor }} için PDF export et
            </button>
        </form>
    @endif
</div>
@endsection