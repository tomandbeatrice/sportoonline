<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>İade Kargo Bilgileri</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4F46E5;">İade Talebiniz Onaylandı</h2>
        
        <p>Merhaba {{ $returnRequest->user->name }},</p>
        
        <p>İade talebiniz (#{{ $returnRequest->return_number }}) onaylandı.</p>
        
        <div style="background-color: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h3 style="margin-top: 0;">Kargo Bilgileri:</h3>
            @if($returnRequest->shipping_label_url)
            <p><strong>Kargo Etiketi:</strong> <a href="{{ url($returnRequest->shipping_label_url) }}">İndir</a></p>
            @endif
            @if($returnRequest->tracking_number)
            <p><strong>Takip Numarası:</strong> {{ $returnRequest->tracking_number }}</p>
            @endif
            @if($returnRequest->shipping_carrier)
            <p><strong>Kargo Firması:</strong> {{ $returnRequest->shipping_carrier }}</p>
            @endif
        </div>
        
        <div style="background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;">
            <h4 style="margin-top: 0;">Önemli Bilgiler:</h4>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Ürünü orijinal ambalajında gönderiniz</li>
                <li>Tüm aksesuarları ve belgeleri ekleyin</li>
                <li>Kargo etiketini paketin üzerine yapıştırın</li>
                <li>Kargo takip numaranızı saklayın</li>
            </ul>
        </div>
        
        <p><strong>İade Tutarı:</strong> {{ number_format($returnRequest->refund_amount, 2) }} TL</p>
        
        <p>İade sürecini <a href="{{ url('/returns/' . $returnRequest->id) }}">buradan</a> takip edebilirsiniz.</p>
        
        <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            Herhangi bir sorunuz varsa, müşteri hizmetlerimizle iletişime geçebilirsiniz.
        </p>
        
        <p style="color: #666; font-size: 12px;">
            SportOnline Ekibi
        </p>
    </div>
</body>
</html>
