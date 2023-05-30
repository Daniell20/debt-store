@extends('layouts.main_layout')
@section('content')
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="col-md-8 col-lg-6 col-xxl-3">
                    <div class="card mb-0">
                        <div class="card-body">
                            {{-- <a href="./index.html" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                <img src="{{ asset('images/logos/dark-logo.svg') }}" width="180" alt="">
                            </a> --}}
                            <p class="text-center">Your Debt Management System</p>
                            <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                                {{ csrf_field() }}
                                <div class="mb-3 {{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="exampleInputtext1" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="exampleInputtext1" name="name" value="{{ old('name') }}" aria-describedby="textHelp">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="mb-3 {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="exampleInputEmail1" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="exampleInputEmail1" name="email" aria-describedby="emailHelp" {{ old('email') }}>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="mb-4 {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="exampleInputPassword1" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="exampleInputPassword1" name="password">
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="mb-4">
                                    <label for="password-confirm" class="form-label">Confirm Password</label>
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign Up</button>
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="fs-4 mb-0 fw-bold">Already have an Account?</p>
                                    <a class="text-primary fw-bold ms-2" href="{{ url('/login') }}">Sign In</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection