@props(['status'])

@php
    $color = match($status) {
        'active' => 'success',
        'passive' => 'danger',
        default => 'secondary'
    };
@endphp

<span class="badge bg-{{ $color }}">
    {{ ucfirst($status) }}
</span>