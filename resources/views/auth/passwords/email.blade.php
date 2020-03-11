@extends('layouts.auth')


@section('content')

<div class="container h-100 d-flex align-items-center">
    <div class="col">
        
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">


                <div class="text-center mb-5">
                    <h2 class="mb-0">Password Reset</h2>
                    <p class="text-gray-600 lead mb-0">Enter your email below to proceed</p>
                </div>

                <span class="clearfix"></span>

                @if (session('status'))
                    <div class="alert alert-success alert-styled-left alert-arrow-left">
                        {{ session('status') }}
                    </div>
                @endif


                <form method="POST" action="{{ route('password.email') }}" aria-label="{{ __('Reset Password') }}">
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


                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-lg btn-block btn-primary">Send Reset Link</button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection