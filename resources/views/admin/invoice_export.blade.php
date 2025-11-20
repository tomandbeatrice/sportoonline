@extends('layouts.admin')

@section('title', 'Toplu Fatura Export')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">📦 Toplu Fatura Export</h2>

    <form method="POST" action="{{ route('admin.invoices.queueExport') }}">
        @csrf
        <button type="submit" class="btn btn-success">Export'u Başlat</button>
    </form>
</div>
@endsection

@section('scripts')
@if(session('success'))
<script>
    Toastify({
        text: "{{ session('success') }}",
        duration: 4000,
        gravity: "top",
        position: "right",
        backgroundColor: "#4fbe87",
    }).showToast();
</script>
@endif

@if(session('error'))
<script>
    Toastify({
        text: "{{ session('error') }}",
        duration: 4000,
        gravity: "top",
        position: "right",
        backgroundColor: "#e74c3c",
    }).showToast();
</script>
@endif
@endsection