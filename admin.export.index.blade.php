@extends('layouts.admin')

@section('title', 'Export Logları')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Export Logları</h1>

        {{-- Durum mesajı --}}
        @if(session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif

        {{-- Filtreleme Formu --}}
        <form method="GET" action="{{ route('admin.export.index') }}" class="flex flex-wrap gap-4 mb-6">
            <input type="text" name="user" value="{{ request('user') }}" placeholder="Kullanıcı Email"
                   class="border px-3 py-2 rounded w-1/4">
            <input type="text" name="vendor" value="{{ request('vendor') }}" placeholder="Vendor Adı"
                   class="border px-3 py-2 rounded w-1/4">
            <input type="date" name="date" value="{{ request('date') }}"
                   class="border px-3 py-2 rounded w-1/4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Filtrele
            </button>
        </form>

        {{-- Export Butonları --}}
        <div class="flex gap-4 mb-6">
            <form method="GET" action="{{ route('admin.export.download') }}">
                <input type="hidden" name="user" value="{{ request('user') }}">
                <input type="hidden" name="vendor" value="{{ request('vendor') }}">
                <input type="hidden" name="date" value="{{ request('date') }}">
                <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Anlık Export Et
                </button>
            </form>

            <form method="GET" action="{{ route('admin.export.queue') }}">
                <input type="hidden" name="user" value="{{ request('user') }}">
                <input type="hidden" name="vendor" value="{{ request('vendor') }}">
                <input type="hidden" name="date" value="{{ request('date') }}">
                <button type="submit"
                        class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                    Arka Planda Export Et
                </button>
            </form>
        </div>

        {{-- Listeleme Tablosu --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-4 py-2 border">Dosya</th>
                        <th class="px-4 py-2 border">Kullanıcı</th>
                        <th class="px-4 py-2 border">Vendor</th>
                        <th class="px-4 py-2 border">IP</th>
                        <th class="px-4 py-2 border">Tarih</th>
                        <th class="px-4 py-2 border">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-2 border">{{ $log->file_name }}</td>
                            <td class="px-4 py-2 border">{{ $log->user_email }}</td>
                            <td class="px-4 py-2 border">{{ $log->vendor_name }}</td>
                            <td class="px-4 py-2 border">{{ $log->ip }}</td>
                            <td class="px-4 py-2 border">{{ $log->created_at }}</td>
                            <td class="px-4 py-2 border">{{ $log->action }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-2 border text-center text-gray-500">
                                Kayıt bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Sayfalama --}}
        <div class="mt-6">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
@endsection