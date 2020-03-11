<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ config('app.url') }}">

    <title>{{ $link->title }}</title>

    <link rel="shortcut icon" href="{{ $link->favicon }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}"> 

    @foreach ($pixelScript as $script)
        <?= $script; ?>
    @endforeach

</head>
<body>


@if ( $link->shorten_only )

    <script>
    setTimeout(function() {
        window.location = '{!! $link->url !!}' 
    }, 100);
    </script>

@else

    <div id="app">

        <div id="app" class="overflow-hidden">
            <iframe src="{{ $link->url }}" class="w-100 h-100" frameborder="0"></iframe>
        </div>

        @foreach ($link->cta as $cta)
            @if ( ! $cta->disabled )
                <cta-preview :cta-id="{!! $cta->id !!}" :link-id="{!! $link->id !!}" link-url="{!! $link->url !!}"></cta-preview>
            @endif
        @endforeach

    </div>

    <script>
        window.appSettings = {
            paymentGateways: {stripe: 0},
        };
    </script>
    <script src="{{ asset('assets/js/link-preview.js') }}"></script>

@endif 


@foreach ($link->scripts as $script)
    @if ( ! $script->disabled )
        <?= $script['code']; ?>
    @endif
@endforeach


</body>
</html>