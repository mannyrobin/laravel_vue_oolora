@include('layouts.header')

<div id="app" class="page-auth has-navbar">

	<nav id="main-navbar" class="navbar navbar-expand navbar-dark bg-primary fixed-top">
		<div class="container">

			<a href="{{ url('/') }}" class="navbar-brand">
				<img class="logo" src="{{ config('settings.logo_light') }}" alt="{{ config('app.name') }}">
			</a>
			
			@yield('navbar-content')

		</div>
	</nav>

    <div id="page-wrapper">

        <main id="page-content">


            @yield('content')

			@if(\Session::has('success'))


				<div class="alert alert-success">
					<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
					<p>{{\Session::get('success')}}</p>
				</div>

			@endif


			@if(\Session::has('error'))

				<div class="alert alert-danger">
					<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
					<p>{{\Session::get('error')}}</p>
				</div>

			@endif
        </main> 

    </div>

</div>

@include('layouts.footer')