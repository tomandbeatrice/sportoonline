@extends('admin.layouts.master')

@section('content')
<h2>Varyantı Düzenle</h2>

@include('admin.partials.alert')

<form action="{{ route('admin.variants.update', $variant->id) }}" method="POST">
    @csrf
    @method('PUT')
    @include('admin.variants._form', ['variant' => $variant])
    <button type="submit" class="btn btn-success">Güncelle</button>
</form>
@endsection