<x-mail::message>
# You have been invited

Hello {{ $invitation->email ?? 'there' }},

You have been invited to join {{ config('app.name') }}.

@if(isset($invitation->inviter))
**Invited by:** {{ $invitation->inviter->name ?? 'Team' }}
@endif

@if(isset($invitation->message))
**Message:**
{{ $invitation->message }}
@endif

<x-mail::button :url="$invitation->accept_url ?? url('/')">
Accept Invitation
</x-mail::button>

This invitation will expire in {{ $invitation->expires_in ?? '7 days' }}.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
