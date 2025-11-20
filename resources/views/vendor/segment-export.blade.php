@extends('layouts.vendor')

@section('title', 'Segment Export')

@section('content')
<div class="container mt-4">
    <h2>Segment Export - {{ request('segment') }}</h2>

    <div id="tabs" class="mb-3">
        <button class="btn btn-outline-primary" onclick="showTab('summary')">Summary</button>
        <button class="btn btn-outline-secondary" onclick="showTab('details')">Details</button>
    </div>

    <div id="tab-content">
        <div id="summary" class="tab-pane active">
            <h4>Summary Overview</h4>
            <p>Total Vendors: {{ $vendors->count() }}</p>
            <p>Segment: {{ request('segment') }}</p>
        </div>

        <div id="details" class="tab-pane" style="display:none;">
            <h4>Vendor Details</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Vendor Name</th>
                        <th>Export Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendors as $vendor)
                    <tr>
                        <td>{{ $vendor->name }}</td>
                        <td>{{ $vendor->export_status }}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="previewModal('{{ $vendor->id }}')">Preview</button>
                        </td>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">Vendor Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalContent">
        <!-- Dynamic content -->
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
function showTab(tab) {
    document.getElementById('summary').style.display = tab === 'summary' ? 'block' : 'none';
    document.getElementById('details').style.display = tab === 'details' ? 'block' : 'none';
}

function previewModal(vendorId) {
    fetch(`/vendor-preview/${vendorId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        });
}
</script>
@endpush