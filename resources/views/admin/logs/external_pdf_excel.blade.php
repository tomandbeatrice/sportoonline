<table>
    <thead>
        <tr>
            <th>Token</th>
            <th>IP</th>
            <th>Erişim Zamanı</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($logs as $log)
        <tr>
            <td>{{ $log->token }}</td>
            <td>{{ $log->ip }}</td>
            <td>{{ $log->accessed_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>