@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-4">Export Logları</h1>

    @if(session('status'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.export.run') }}" class="mb-6">
        @csrf
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Export Başlat
        </button>
    </form>

    <table class="table-auto w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2">Dosya</th>
                <th class="px-4 py-2">Kullanıcı</th>
                <th class="px-4 py-2">Vendor</th>
                <th class="px-4 py-2">IP</th>
                <th class="px-4 py-2">Tarih</th>
                <th class="px-4 py-2">İşlem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $log->file_name }}</td>
                    <td class="px-4 py-2">{{ $log->user_email ?? 'Anonim' }}</td>
                    <td class="px-4 py-2">{{ $log->vendor_name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $log->ip }}</td>
                    <td class="px-4 py-2">{{ $log->created_at }}</td>
                    <td class="px-4 py-2">{{ $log->action }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
<h3>🧹 Haftalık Cleanup Özeti</h3>
<table class="table">
    <thead>
        <tr>
            <th>Hafta</th>
            <th>Toplam Boyut (MB)</th>
            <th>Silinen Dosya Sayısı</th>
        </tr>
    </thead>
    <tbody id="log-summary-body"></tbody>
</table>

<script>
fetch('/admin/export-logs/summary')
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById('log-summary-body');
        data.forEach(log => {
            const row = `<tr>
                <td>${log.week}</td>
                <td>${log.size_mb}</td>
                <td>${log.count}</td>
            </tr>`;
            tbody.innerHTML += row;
        });
    });
</script>