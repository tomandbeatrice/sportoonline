<ul class="list-group mb-4">
    @forelse($variants as $variant)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>{{ $variant->name }}</span>
            <span class="badge bg-primary rounded-pill">{{ number_format($variant->price, 2) }} ₺</span>
        </li>
    @empty
        <li class="list-group-item text-muted">Varyasyon bulunamadı.</li>
    @endforelse
</ul>