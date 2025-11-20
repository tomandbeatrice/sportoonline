@extends('admin.layouts.app')

@section('title', 'Vendor Export Summary')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📊 Vendor Export Özet Tablosu</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.exports.summary') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="vendor" class="form-label">Vendor Seç</label>
            <select name="vendor" id="vendor" class="form-select">
                <option value="">Tümü</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" {{ request('vendor') == $vendor->id ? 'selected' : '' }}>
                        {{ $vendor->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="date" class="form-label">Tarih</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Filtrele</button>
        </div>
    </form>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Vendor</th>
                <th>Toplam Export Sayısı</th>
                <th>En Son Export</th>
                <th>PDF İndir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summary as $row)
                <tr>
                    <td>{{ $row->vendor_name }}</td>
                    <td>{{ $row->total_exports }}</td>
                    <td>{{ $row->last_export_date }}</td>
                    <td>
                        <a href="{{ route('admin.exports.downloadSummary', ['vendor' => $row->vendor_id]) }}"
                           class="btn btn-sm btn-outline-success">
                            PDF
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Veri bulunamadı.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection