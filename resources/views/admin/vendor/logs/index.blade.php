@extends('layouts.admin')

@section('content')
<h2>Vendor Erişim Logları</h2>

<form method="GET">
    <input type="text" name="vendor_id" placeholder="Vendor ID" value="{{ request('vendor_id') }}">
    <input type="date" name="date_from" value="{{ request('date_from') }}">
    <input type="date" name="date_to" value="{{ request('date_to') }}">
    <button type="submit">Filtrele</button>
</form>

<table>
    <thead>
        <tr>
            <th>Vendor</th>
            <th>IP</th>
            <th>Agent</th>
            <th>Tarih</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
        <tr>
            <td>{{ $log->vendor_id }}</td>
            <td>{{ $log->ip }}</td>
            <td>{{ $log->user_agent }}</td>
            <td>{{ $log->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $logs->links() }}
@endsection