@props(['type' => 'info', 'message'])

@php
    $bg = [
        'success' => 'bg-success text-white',
        'error' => 'bg-danger text-white',
        'info' => 'bg-info text-white',
        'warning' => 'bg-warning text-dark',
    ][$type] ?? 'bg-secondary text-white';
@endphp

<div class="alert {{ $bg }} alert-dismissible fade show mb-3 shadow-sm" role="alert" style="z-index: 9999;">
    <strong>{{ ucfirst($type) }}:</strong> {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
</div>