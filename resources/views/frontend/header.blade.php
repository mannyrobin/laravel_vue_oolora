<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <title>{{ config('app.name') }}</title>

    <link rel="shortcut icon" href="{{ config('settings.favicon') }}" type="image/x-icon">
	<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}"> 

	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

    <link href="{{ url('assets/frontend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
   <link href="{{ url('assets/frontend/css/style.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/frontend/css/custom.css') }}" rel="stylesheet" type="text/css" />

    <style type="text/css">
	   {{ config('settings.custom_css') }}
    </style>
	
	<?= config('settings.analytics_code'); ?>
	
</head>
<body>

 <div class="page-wrapper">

    <header id="site-header">
        <div id="header-wrap" class="box-shadow">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">

                        <nav class="navbar navbar-expand-lg">
                            <a class="navbar-brand logo" href="{{ url('/') }}">
                                <img id="logo-img" class="img-center" src="{{ config('settings.logo_dark') }}" alt="">
                            </a>

                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation"> <span></span>
                                <span></span>
                                <span></span>
                            </button>

                            <div class="collapse navbar-collapse" id="navbarNavDropdown">
								<ul class="nav navbar-nav ml-auto mr-auto">
                                    <li class="nav-item"> <a class="nav-link active" href="#home">Home</a></li>
                                    <li class="nav-item"> <a class="nav-link" href="#how-it-works">How It Works</a></li>
                                    <li class="nav-item"> <a class="nav-link" href="#features">Features</a></li>
                                    <li class="nav-item"> <a class="nav-link" href="#pricing">Pricing</a></li>
                                    <li class="nav-item"> <a class="nav-link" href="#testimonials">Testimonials</a></li>
                                </ul>
                            </div>

                            <a class="btn btn-outline-primary btn-sm mr-3" href="{{ url('login') }}">Login</a>
                            <a class="btn btn-primary btn-sm" href="{{ url('register') }}">Signup</a>

                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </header>
