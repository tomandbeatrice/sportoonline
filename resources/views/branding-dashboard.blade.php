{{-- Branding Preview Gallery --}}
<div class="card mb-4">
    <div class="card-header">🖼️ Branding Preview Gallery</div>
    <div class="card-body">
        <div class="row">
            @foreach($vendors as $vendor)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center" style="background-color: {{ $vendor->branding_color }};">
                            <img src="{{ asset('storage/' . $vendor->branding_logo) }}" alt="Logo" class="mb-2" style="max-height: 60px;">
                            <h5 style="font-family: {{ $vendor->branding_font }};">{{ $vendor->name }}</h5>
                            <p class="small">Font: {{ $vendor->branding_font }}<br>Color: {{ $vendor->branding_color }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>