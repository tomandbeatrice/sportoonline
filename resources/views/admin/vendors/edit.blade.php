@extends('layouts.admin')

@section('content')
<h1>Edit Vendor</h1>

<form action="{{ route('admin.vendors.update', $vendor->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name">Vendor Name</label>
        <input type="text" name="name" class="form-control" value="{{ $vendor->name }}" required>
    </div>

    <div class="form-group">
        <label for="logo_url">Logo URL</label>
        <input type="text" name="logo_url" class="form-control" value="{{ $vendor->logo_url }}">
    </div>

    <div class="form-group">
        <label for="primary_color">Primary Color</label>
        <input type="color" name="primary_color" class="form-control" value="{{ $vendor->primary_color ?? '#000000' }}">
    </div>

    <div class="form-group">
        <label for="secondary_color">Secondary Color</label>
        <input type="color" name="secondary_color" class="form-control" value="{{ $vendor->secondary_color ?? '#ffffff' }}">
    </div>

    <button type="submit" class="btn btn-success">Update</button>
</form>
@endsection