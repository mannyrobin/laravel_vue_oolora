@if ( auth()->user() )
<script>
	window.appUser = {
		id: "{!! auth()->user()->id !!}",
		name: "{!! auth()->user()->name !!}",
		email: "{!! auth()->user()->email !!}",
		avatar: "{!! asset( Storage::url(auth()->user()->avatar) ) !!}",
		emailVerified: {!! ( (bool)auth()->user()->hasVerifiedEmail() ? 'true' : 'false' ) !!},
		permissions: {!! auth()->user()->getAllPermissions() !!},
		unreadNotifications: 0
	};
</script>
@endif

<script>
	window.appSettings = {
		appUrl: "{!! config('app.url') !!}",
		appName: "{!! config('app.name') !!}",
		logoDark: "{!! config('settings.logo_dark') !!}",
		logoLight: "{!! config('settings.logo_light') !!}",
		currencyCode: "{!! config('settings.currency_code') !!}",
		currencySymbol: "{!! config('settings.currency_symbol') !!}",
		currentVersion: "{!! config('settings.software.version') !!}",
		latestVersion: "{!! config('settings.software.latest_version') !!}",
		linkShortenDomain: "{!! ( config('settings.link_shorten_domain') ? config('settings.link_shorten_domain') : parse_url(config('app.url'), PHP_URL_HOST) .'/l' ) !!}",
		stripePublisherKey: "{!! config('services.stripe.key') !!}",
		serverIpAddress: "{!! config('settings.server_ip_address') !!}",
		paymentGateways: {
			stripe: "{!! config('services.stripe.enable') !!}",
			paypal: "{!! config('services.paypal.enable') !!}"
		},
	};
</script>