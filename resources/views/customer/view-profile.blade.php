@extends('layouts.main_layout')
@section('content')
    @include('layouts.sidebar')

    <div class="body-wrapper">

        @include('layouts.navbar')

        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <section>
                        <div class="container py-5">
                            <div class="row d-flex justify-content-center align-items-center">
                                <div class="col col-lg-6 mb-4 mb-lg-0">
                                    <div class="card mb-3" style="border-radius: .5rem;">
                                        <div class="row g-0">
                                            @foreach ($view_customer as $view)
                                                <div class="col-md-4 gradient-custom text-center text-white"
                                                    style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                                                    <img src="{{ asset('images/products/s7.jpg') }}" alt="Avatar"
                                                        class="img-fluid my-5" style="width: 80px;" />
                                                    <h5>{{ $view->name }}</h5>
                                                    <i class="far fa-edit mb-5"></i>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="card-body p-4">
                                                        <h6>Information</h6>
                                                        <hr class="mt-0 mb-4">
                                                        <div class="row pt-1">
                                                            <div class="col-6 mb-3">
                                                                <h6>Email</h6>
                                                                <p class="text-muted">{{ $view->email }}</p>
                                                            </div>
                                                            <div class="col-6 mb-3">
                                                                <h6>ID</h6>
                                                                <p class="text-muted">{{ $view->customer_id }}</p>
                                                            </div>
                                                        </div>
                                                        <h6>Others</h6>
                                                        <hr class="mt-0 mb-4">
                                                        <div class="row pt-1">
                                                            <div class="col-12 mb-3">
                                                                <h6>Recent Debt</h6>
                                                                <p class="text-muted">Dolor sit amet</p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-start">
                                                            <a href="#!"><i
                                                                    class="fab fa-facebook-f fa-lg me-3"></i></a>
                                                            <a href="#!"><i class="fab fa-twitter fa-lg me-3"></i></a>
                                                            <a href="#!"><i class="fab fa-instagram fa-lg"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
