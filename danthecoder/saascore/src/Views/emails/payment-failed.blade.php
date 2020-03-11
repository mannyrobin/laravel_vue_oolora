@component('mail::message')

Hey {{ $payment['user'] }},

We weren't able to renew your membership subscription because the payment method on file was declined. @if ( $payment['next_attempt'] )Another attempt will be made on **{{ $payment['next_attempt'] }}**.@endif


@component('mail::panel')
Some common issues involve:
- There's a problem with your bank
- Your payment card has expired
- There are insufficient funds in your account
@endcomponent


@if ( is_null($payment['next_attempt']) )
**This is your final notice, please check and update your payment details to avoid any service interruptions.**
@else
Please check and update your payment details if necessary.
@endif


@component('mail::button', ['url' => $payment['action_url']])
Update Payment Details
@endcomponent


If you feel this is a mistake on our end, please contact our support team for assistance at <{{ config('settings.support_email') }}>


Regards,<br>
The {{ config('app.name') }} Team


@component('mail::subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: ',
    [
        'actionText' => 'Update Payment Details'
    ]
)
[{{ $payment['action_url'] }}]({!! $payment['action_url'] !!})
@endcomponent

@endcomponent