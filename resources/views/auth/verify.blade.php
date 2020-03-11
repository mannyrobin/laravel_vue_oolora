@extends('layouts.auth')

@section('content')

<div class="container h-100 d-flex align-items-center">
    <div class="col">
        
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-5">

                @if (session('resent'))
                    <div class="alert alert-success alert-styled-left alert-arrow-left">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif


                <div class="text-center mb-4">
                    <i class="fal fa-envelope fa-4x mb-2"></i>
                    <span class="badge badge-danger badge-pill animate-pulse" style="position: relative; right: 15px; top: -35px;">1</span>

                    <h2 class="mb-0">Verify Your Email Address</h2>
                    <p class="lead mb-0">Please check your email for a verification link.</p>
                </div>


                <div class="text-center">
                    <p>Didn't receive the verification email?</p>
                    <a href="{{ route('verification.resend') }}" class="btn btn-lg btn-primary">Resend Verification</a>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
