@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Segment Listesi</h4>
        <a href="{{ route('admin.segment.export', ['vendor_id' => $vendor->id, 'segment' => 'health']) }}"
           class="btn btn-sm btn-outline-primary"
           target="_blank">
            <i class="fas fa-file-export"></i> Export Health Segment
        </a>
    </div>

    {{-- Segment tablosu --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Segment Adı</th>
                        <th>Açıklama</th>
                        <th>Oluşturulma</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($segments as $segment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $segment->name }}</td>
                            <td>{{ $segment->description }}</td>
                            <td>{{ $segment->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $segments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection