@extends('layouts.auth')


@section('navbar-content')

<form class="form-inline">
    <span class="navbar-text mr-2 d-none d-md-block">Not Registered?</span>
    <a href="{{ url('register') }}" class="btn btn-sm btn-light">Create Account</a>
</form>

@endsection


@section('content')

<div class="container h-100 d-flex align-items-center">
    <div class="col">
        
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">


                <div class="text-center mb-5">
                    <h2 class="mb-0">Account Login</h2>
                    <p class="text-gray-600 lead mb-0">Enter your login credentials below</p>
                </div>

                <span class="clearfix"></span>

                <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                    @csrf
                    
                    <label class="form-control-label">Email Address</label>
                    <div class="form-group form-group-feedback form-group-feedback-left">
                        <input type="email" placeholder="john@example.com" class="form-control form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                        <div class="form-control-feedback form-control-feedback-lg">
                            <i class="fal fa-envelope"></i>
                        </div>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback">{{ $errors->first('email') }}</span>
                        @endif
                    </div>


                    <label class="form-control-label">Password</label>
                    <div class="form-group form-group-feedback form-group-feedback-left">
                        <input type="password" placeholder="password" class="form-control form-control-lg {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                        <div class="form-control-feedback form-control-feedback-lg">
                            <i class="fal fa-key"></i>
                        </div>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback">{{ $errors->first('password') }}</span>
                        @endif
                    </div>


                    <div class="row">

                        <div class="form-group col">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="remember" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="remember">Remember Me</label>
                            </div>
                        </div>

                        <div class="col text-right">
                            <a class="text-default" href="{{ route('password.request') }}">Lost Password?</a>
                        </div>

                    </div>


                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-lg btn-block btn-primary">Sign in</button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
