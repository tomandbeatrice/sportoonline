<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body>
    @include('admin.partials.navbar')

    <div class="container mt-4">
        <x-alerts />
        @yield('content')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    @if(session('success'))
    <script>
        Toastify({
            text: "{{ session('success') }}",
            duration: 4000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "#28a745",
        }).showToast();
    </script>
    @endif

    @if(session('toast_error'))
<script>
    Toastify({
        text: "{{ session('toast_error') }}",
        backgroundColor: "#ff4d4f",
        duration: 4000
    }).showToast();
</script>
@endif
</body>
</html>