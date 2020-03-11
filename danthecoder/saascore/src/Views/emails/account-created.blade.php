@component('mail::message')

Hey {{ $account['name'] }},

An account was successfully created for you at **{{ config('app.name') }}** by our team, you will find your log in credentials below. 

@component('mail::panel')
Email: **{{ $account['email'] }}** <br>
Password: **{{ $account['password'] }}**
@endcomponent


@component('mail::button', ['url' => $account['action_url']])
Access Account
@endcomponent


Once you have logged in, it is encouraged that you change your password.

Regards,<br>
The {{ config('app.name') }} Team


@component('mail::subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: ',
    [
        'actionText' => 'Access Account'
    ]
)
[{{ $account['action_url'] }}]({!! $account['action_url'] !!})
@endcomponent

@endcomponent
