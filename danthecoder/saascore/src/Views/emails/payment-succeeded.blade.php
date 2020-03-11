@component('mail::message')

Hey {{ $subscription['user'] }},

Thanks for using {{ config('app.name') }}. Your subscription payment has been successfully processed. Your payment details are shown below.

@component('mail::panel')
Date: **{{ $subscription['date'] }}** <br>
Amount: **{{ $subscription['amount'] }}**
@endcomponent


@component('mail::button', ['url' => $subscription['action_url']])
View Invoice
@endcomponent


If you have any questions, please contact our support team for assistance at <{{ config('settings.support_email') }}>


Regards,<br>
The {{ config('app.name') }} Team


@component('mail::subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: ',
    [
        'actionText' => 'View Invoice'
    ]
)
[{{ $subscription['action_url'] }}]({!! $subscription['action_url'] !!})
@endcomponent

@endcomponent
