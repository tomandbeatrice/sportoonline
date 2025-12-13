<x-mail::message>
# Return Shipping Code

Hello {{ $returnRequest->user->name ?? 'Customer' }},

Your return request **#{{ $returnRequest->return_number }}** has been approved.

## Shipping Information

**Return Number:** {{ $returnRequest->return_number }}

**Tracking Code:** {{ $returnRequest->tracking_number ?? 'Will be provided' }}

@if(isset($returnRequest->shipping_carrier))
**Carrier:** {{ $returnRequest->shipping_carrier }}
@endif

@if(isset($returnRequest->shipping_label_url))
<x-mail::button :url="url($returnRequest->shipping_label_url)">
Download Shipping Label
</x-mail::button>
@endif

## Return Instructions

1. Package the item securely in its original packaging if possible
2. Attach the shipping label to the package
3. Drop off the package at the nearest shipping location
4. Keep your tracking number for reference

**Refund Amount:** ₺{{ number_format($returnRequest->refund_amount, 2) }}

Once we receive and inspect your return, the refund will be processed according to your selected refund method.

If you have any questions, please contact our customer support.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
