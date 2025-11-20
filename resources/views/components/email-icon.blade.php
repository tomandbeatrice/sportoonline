@php
/**
 * Email icon component
 * Usage: <x-email-icon name="bell" alt="Bildirim" strategy="img" />
 * Strategy resolution order: explicit $strategy attribute -> env EMAIL_ICON_STRATEGY -> 'emoji'
 */
$envStrategy = env('EMAIL_ICON_STRATEGY', 'emoji');
$strategy = isset($strategy) && $strategy ? $strategy : $envStrategy;
$icons = [
    'bell' => '🔔',
    'box' => '📦',
    'mail' => '📝',
    'check' => '✅',
    'cross' => '❌',
    'party' => '🎉',
    'ticket' => '🎫',
    'rocket' => '🚀'
];
$name = isset($name) ? $name : '';
$alt = isset($alt) ? $alt : '';
@endphp

@if($strategy === 'img')
    <img src="{{ asset('emails/icons/'.$name.'.svg') }}" alt="{{ $alt }}" width="20" height="20" style="vertical-align: middle; margin-right: 6px;" />
@else
    <span aria-hidden="true" style="vertical-align: middle; margin-right: 6px;">{{ $icons[$name] ?? '' }}</span>
    @if(!empty($alt))
        <span class="sr-only">{{ $alt }}</span>
    @endif
@endif
