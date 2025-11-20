@extends('layouts.admin')

@section('content')
<h1>Create Vendor</h1>

<form action="{{ route('admin.vendors.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="name">Vendor Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="logo_url">Logo URL</label>
        <input type="text" name="logo_url" class="form-control">
    </div>

    <div class="form-group">
        <label for="primary_color">Primary Color</label>
        <input type="color" name="primary_color" class="form-control" value="#000000">
    </div>

    <div class="form-group">
        <label for="secondary_color">Secondary Color</label>
        <input type="color" name="secondary_color" class="form-control" value="#ffffff">
    </div>

    <button type="submit" class="btn btn-primary">Create</button>
</form>
@endsection