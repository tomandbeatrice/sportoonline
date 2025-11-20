@extends('layouts.admin')

@section('title', 'Vendor PDF Branding Analizi')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">🎯 Vendor PDF Başlık Analizi</h4>

    <span class="badge bg-primary">Vendor ID: {{ $vendorId }}</span>

    <div class="row mt-4">
        <div class="col-md-6">
            <h5>📊 Başlık Grupları</h5>
            <ul class="list-group">
                @forelse ($grouped as $title => $count)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $title }}
                        <span class="badge bg-secondary">{{ $count }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Hiç başlık bulunamadı.</li>
                @endforelse
            </ul>
        </div>

        <div class="col-md-6">
            <h5>📄 Dosya Bazlı Başlıklar</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Dosya</th>
                        <th>Başlık</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($titles as $item)
                        <tr>
                            <td>{{ $item['file'] }}</td>
                            <td>{{ $item['title'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-muted text-center">Veri bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection