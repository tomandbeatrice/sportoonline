@extends('layouts.admin')

@section('content')
<h3>Dış Erişim Logları</h3>

<form method="GET" action="{{ route('admin.externalPdfLogs.index') }}">
    <input type="text" name="ip" placeholder="IP filtrele" value="{{ request('ip') }}">
    <input type="date" name="from" value="{{ request('from') }}">
    <input type="date" name="to" value="{{ request('to') }}">
    <button type="submit">Filtrele</button>
    <a href="{{ route('admin.externalPdfLogs.export', request()->all()) }}" style="margin-left: 10px;">Excel'e Aktar</a>
</form>

<table>
    <thead>
        <tr>
            <th>Token</th>
            <th>IP</th>
            <th>Erişim Zamanı</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($logs as $log)
        <tr>
            <td>{{ $log->token }}</td>
            <td>{{ $log->ip }}</td>
            <td>{{ $log->accessed_at }}</td>
        </tr>
        @empty
        <tr><td colspan="3">Log bulunamadı</td></tr>
        @endforelse
    </tbody>
</table>
@endsection