@extends('layouts.main_layout')
@section('content')
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100">
                <div class="col-md-8 col-lg-6 col-xxl-3">
                    <div class="card mb-0">
                        <div class="card-body">
                            <a href="#" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                <img src="{{ asset('images/logos/debstorelogo.png') }}" width="100%" alt="">
                            </a>
                            <p class="text-center">Your Debt Management System</p>
                            <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                {{ csrf_field() }}
                                <div class="mb-3 {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="exampleInputEmail1" class="form-label">Username</label>
                                    <input type="email" class="form-control" name="email" id="exampleInputEmail1"
                                        aria-describedby="emailHelp" value="{{ old('email') }}">

                                    @if ($errors->has('email'))
                                        <span class="help-block text-danger">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="mb-4 {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="exampleInputPassword1" class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="exampleInputPassword1">
                                    @if ($errors->has('password'))
                                        <span class="help-block text-danger">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input primary" type="checkbox" value=""
                                            id="flexCheckChecked" {{ old('remember') ? 'checked' : '' }} checked>
                                        <label name="remember" class="form-check-label text-dark" for="flexCheckChecked">
                                            Remeber this Device
                                        </label>
                                    </div>
                                    <!-- <a class="text-primary fw-bold" href="{{ route('password.request') }}">Forgot
                                        Password ?</a> -->
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign
                                    In</button>
                                <div class="d-flex align-items-center justify-content-center">
                                    {{-- <p class="fs-4 mb-0 fw-bold">New to Debt Store?</p> --}}
                                    <!-- <a class="text-primary fw-bold ms-2"
                                        href="{{ route('register') }}">Create an account</a> -->
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
    <script>
        $(document).ready(function() {
            const errorMessage = "{{ session('error') }}";
            if (errorMessage) {
                iziToast.error({
                    title: 'Error',
                    message: errorMessage,
                    position: 'topRight',
                    timeout: 6000,
                    transitionIn: "bounceInDown",
                    transitionOut: "flipOutX",
                });
            }
        })
    </script>
@endsection
