@extends('layouts.auth')


@section('navbar-content')


@endsection


@section('content')

    <div class="container  d-flex align-items-center">
        <div class="col">

            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5 col-xl-5">

                    <span class="clearfix"></span>
                    <form action="{{route('redeem.code')}}" class="w-100" method="post">
                        @csrf
                        <div class="text-center mb-5">
                            <h2 class=""></h2>

                        </div>
                        <label class="col-form-label">Enter your Redemption Code</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="code" placeholder="Enter your code">
                            <input type="hidden" name="user_id" value="{{Auth::id()}}">
                            @if ($errors->has('code'))
                                <span class="invalid-feedback">{{ $errors->first('code') }}</span>
                            @endif
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>

                        </div>

                    </form>




                </div>
            </div>

        </div>
    </div>


@endsection