<table>
    <thead>
        <tr>
            <th>Vendor</th>
            <th>Token</th>
            <th>Durum</th>
            <th>Toplam Erişim</th>
            <th>Son Erişim</th>
            <th>En Çok Erişilen Dosya</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vendors as $v)
        <tr>
            <td>{{ $v['Vendor'] }}</td>
            <td>{{ $v['Token'] }}</td>
            <td>{{ $v['Durum'] }}</td>
            <td>{{ $v['Toplam Erişim'] }}</td>
            <td>{{ $v['Son Erişim'] }}</td>
            <td>{{ $v['En Çok Erişilen Dosya'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>