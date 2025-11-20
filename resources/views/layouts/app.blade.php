<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

    {{-- Vite asset çağrıları --}}
    <link rel="stylesheet" href="{{ asset('build/assets/app-CEE71Pqp.css') }}">
</head>
<body>
    @yield('content')

    {{-- Vite JS --}}
    <script type="module" src="{{ asset('build/assets/app-CZ2GacsZ.js') }}"></script>
</body>
</html>