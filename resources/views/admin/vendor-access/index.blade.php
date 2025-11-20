@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Vendor Erişim Logları</h4>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Vendor</th>
                <th>Toplam Erişim</th>
                <th>PDF Önizleme</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vendors as $v)
            <tr>
                <td>{{ $v->name }}</td>
                <td>{{ $v->externalPdfAccessLogs->count() }}</td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="loadVendorPreview({{ $v->id }})">
                        Önizle
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- ✅ Vendor PDF Preview Modal -->
<div class="modal fade" id="vendorPreviewModal" tabindex="-1" aria-labelledby="vendorPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="vendorPreviewModalLabel">
                    PDF Önizleme – <span id="vendorPreviewTitle" class="fw-bold"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div id="vendorPreviewList" class="d-flex flex-wrap gap-2 mb-3">
                    <!-- PDF dosya butonları buraya JS ile gelecek -->
                </div>
                <div class="border rounded overflow-hidden">
                    <iframe id="vendorPdfFrame" src="" width="100%" height="600px" style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadVendorPreview(vendorId) {
    fetch(`/admin/external-pdf/vendor-access/preview/${vendorId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('vendorPreviewTitle').textContent = data.vendor;
            const list = document.getElementById('vendorPreviewList');
            const frame = document.getElementById('vendorPdfFrame');
            list.innerHTML = '';
            frame.src = '';

            data.files.forEach(file => {
                const btn = document.createElement('button');
                btn.className = 'btn btn-outline-dark btn-sm me-2 mb-2';
                btn.textContent = file;
                btn.onclick = () => {
                    const encodedFile = encodeURIComponent(file);
                    frame.src = `/admin/external-pdf/view?file=${encodedFile}&token=secure123`;
                };
                list.appendChild(btn);
            });

            $('#vendorPreviewModal').modal('show');
        });
}
</script>
@endpush