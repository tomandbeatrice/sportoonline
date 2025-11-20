{{-- Varyant Durum Formunu Partial Olarak Dahil Et --}}
@include('products.partials._status-form', ['variant' => $variant])

{{-- Alert Bileşenleri --}}
@if (session('success'))
    <x-alert type="success" :message="session('success')" />
@endif

@if (session('error'))
    <x-alert type="danger" :message="session('error')" />
@endif

@if ($errors->any())
    <x-alert type="warning" :message="$errors->first()" />
@endif