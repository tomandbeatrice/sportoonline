@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Vendor Listesi</h4>

    <table class="table table-bordered table-hover table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Vendor Adı</th>
                <th>Email</th>
                <th>Oluşturulma</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendors as $v)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $v->name }}</td>
                <td>{{ $v->email }}</td>
                <td>{{ $v->created_at->format('d.m.Y') }}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="loadVendorLogs({{ $v->id }})">
                        Detay
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Vendor Log Modal -->
<div class="modal fade" id="vendorLogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Erişim Logları – <span id="vendorLogTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>IP</th>
                            <th>Dosya</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody id="vendorLogBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadVendorLogs(vendorId) {
    fetch(`/admin/external-pdf/vendor-access/logs/${vendorId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('vendorLogTitle').textContent = data.vendor;
            const tbody = document.getElementById('vendorLogBody');
            tbody.innerHTML = '';

            data.logs.forEach(log => {
                const row = `<tr>
                    <td>${log.ip}</td>
                    <td>${log.file}</td>
                    <td>${log.accessed_at}</td>
                </tr>`;
                tbody.innerHTML += row;
            });

            $('#vendorLogModal').modal('show');
        });
}
</script>
@endpush