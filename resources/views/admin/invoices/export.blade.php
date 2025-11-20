@extends('layouts.admin')

@section('title', 'Toplu Fatura Export')

@section('content')
<div class="container-fluid py-4">
    <h4 class="mb-4">Toplu Fatura Export</h4>

    {{-- Başarılı Export Mesajı --}}
    @if(session('success') && session('download_token'))
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            <span>{{ session('success') }}</span>
            <a href="{{ route('admin.invoices.download', ['token' => session('download_token')]) }}"
               class="btn btn-sm btn-primary">
               <i class="fa fa-download me-1"></i> İndir
            </a>
        </div>
    @endif

    {{-- ZIP Export --}}
    <form method="POST" action="{{ route('admin.invoices.exportZip') }}">
        @csrf
        <div class="row mb-4">
            <div class="col-md-4">
                <label>Vendor Seç</label>
                <select name="vendor_id" class="form-select">
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Tarih Aralığı</label>
                <input type="text" name="date_range" class="form-control" placeholder="01.08.2025 - 04.08.2025">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa fa-file-zip"></i> ZIP Export
                </button>
            </div>
        </div>
    </form>

    {{-- Toplu Download --}}
    <form method="POST" action="{{ route('admin.invoices.bulkDownload') }}">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Admin Seç</label>
                <select name="admin_id" class="form-select">
                    <option value="">Tüm Adminler</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Vendor Seç</label>
                <select name="vendor_id" class="form-select">
                    <option value="">Tüm Vendorlar</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Tarih Aralığı</label>
                <input type="date" name="start_date" class="form-control mb-1" required>
                <input type="date" name="end_date" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success">
            <i class="fa fa-download"></i> Toplu Download
        </button>
    </form>
</div>
@endsection