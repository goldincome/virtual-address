<x-mail::message>
# Payment Failed - Action Required

Hello {{ $user->name }},

We were unable to process payment for your recent invoice.

@if($amountDue)
**Amount Due:** {{ currencyFormatter($amountDue) }}
@endif
@if($invoiceId)
**Invoice:** {{ $invoiceId }}
@endif
@if($attemptCount)
**Attempt:** {{ $attemptCount }}
@endif

Please update your payment method or pay the outstanding amount to avoid service interruption (especially for mail forwarding/scanning services).

@if($hostedInvoiceUrl)
<x-mail::button :url="$hostedInvoiceUrl">
Pay Invoice Now
</x-mail::button>
@else
<x-mail::button :url="route('dashboard')">
Go to Dashboard
</x-mail::button>
@endif

If you have already updated your payment details, please ignore this message.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
