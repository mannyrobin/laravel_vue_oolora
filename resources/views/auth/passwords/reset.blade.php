@extends('layouts.auth')


@section('content')

    <div class="container h-100 d-flex align-items-center">
        <div class="col">
            
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5 col-xl-5">


                    <div class="text-center mb-5">
                        <h2 class="mb-0">Password Reset</h2>
                        <p class="text-gray-600 lead mb-0">Enter your new password and current email</p>
                    </div>

                    <span class="clearfix"></span>

                    <form method="POST" action="{{ route('password.request') }}" aria-label="{{ __('Reset Password') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        
                        <label class="form-control-label">Email address</label>
                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input type="email" placeholder="john@example.com" class="form-control form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
                            <div class="form-control-feedback form-control-feedback-lg">
                                <i class="fal fa-envelope"></i>
                            </div>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">{{ $errors->first('email') }}</span>
                            @endif
                        </div>


                        <div class="row">
                            <div class="col">

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

                            </div>
                            <div class="col">

                                <label class="form-control-label">Confirm Password</label>
                                <div class="form-group form-group-feedback form-group-feedback-left">
                                    <input type="password" placeholder="password" class="form-control form-control-lg" name="password_confirmation" required>
                                    <div class="form-control-feedback form-control-feedback-lg">
                                        <i class="fal fa-key"></i>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-lg btn-block btn-primary">Reset Password</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
