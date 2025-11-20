@extends('admin.layouts.master')

@section('content')
<h2>Yeni Varyant Oluştur</h2>

@include('admin.partials.alert')

<form action="{{ route('admin.variants.store') }}" method="POST">
    @csrf
    @include('admin.variants._form', ['variant' => null])
    <button type="submit" class="btn btn-primary">Kaydet</button>
</form>
@endsection