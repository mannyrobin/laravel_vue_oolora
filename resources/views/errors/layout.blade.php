@include('layouts.header')


<div class="has-navbar h-100">

    <nav id="main-navbar" class="navbar navbar-expand navbar-light bg-white fixed-top">
        <div class="container">

            <a href="{{ url('/') }}" class="navbar-brand">
                <img class="logo" src="{{ config('settings.logo_dark') }}" alt="{{ config('app.name') }}">
            </a>

        </div>
    </nav>

    <div id="page-wrapper">

        <main id="page-content">

            <div class="container h-100 d-flex align-items-center">
                <div class="col">
                    
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-8 text-center">

                            <h1 class="display-2 font-weight-bold mb-0">@yield('code', __('Oh no'))</h1>
                            <p class="lead">@yield('message')</p>
                            <a class="btn btn-primary mt-3 text-uppercase" href="{{ app('router')->has('home') ? route('home') : url('/') }}">{{ __('Go Home') }}</a>

                        </div>
                    </div>

                </div>
            </div>

        </main> 

    </div>

</div>
