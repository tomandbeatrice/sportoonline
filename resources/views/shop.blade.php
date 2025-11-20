@extends('layouts.front')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Ürün Vitrini</h2>
    <div class="row">
        @foreach($products as $product)
            @foreach($product->variations as $variation)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset('storage/' . $variation->image) }}" class="card-img-top" alt="Görsel">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">Renk: {{ $variation->color }}</p>
                            <p class="card-text">Stok: {{ $variation->stock }}</p>
                            <a href="#" class="btn btn-primary">Sepete Ekle</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</div>
@endsection