@extends('layouts.main_layout')
@section('content')
@include('layouts.sidebar')
        <!--  Main wrapper -->
        <div class="body-wrapper">
            @include('layouts.navbar')
            <!--  Header End -->
            <div class="container-fluid">
                <!--  Row 1 -->
                <div class="row">
                    <div class="col-lg-8 d-flex align-items-strech">
                        <div class="card w-100">
                            <div class="card-body">
                                <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                                    <div class="mb-3 mb-sm-0">
                                        <h5 class="card-title fw-semibold">Sales Overview</h5>
                                    </div>
                                    <div class="charts-card">
                                        <p class="chart-title">Top 5 Debtors</p>
                                        <div id="bar-chart"></div>
                                      </div>
                                </div>
                                <div id="chart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <!-- Yearly Breakup -->
                                <div class="card overflow-hidden">
                                    <div class="card-body p-4">
                                        <h5 class="card-title mb-9 fw-semibold">Customers</h5>
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <h4 class="fw-semibold mb-3">{{ array_sum($customerCounts) }}</h4>
                                                <div class="d-flex align-items-center mb-3">
                                                    <span
                                                        class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-arrow-up-left text-success"></i>
                                                    </span>
                                                    <p class="text-dark me-1 fs-3 mb-0">{{ number_format($percentageChange, 2) }}%</p>
                                                    <p class="fs-3 mb-0">last month</p>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-4">
                                                        <span
                                                            class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                                                        <span class="fs-2">{{ end($months) }}</span>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="round-8 bg-light-primary rounded-circle me-2 d-inline-block"></span>
                                                        <span class="fs-2">{{ count($months) >= 2 ? $months[count($months) - 2] : '' }}</span>
                                                    </div>
                                                </div>
                                            </div>                                            
                                            <div class="col-4">
                                                <div class="d-flex justify-content-center">
                                                    <div id="customer_count"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <!-- Debt Product -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row alig n-items-start">
                                            <div class="col-8">
                                                <h5 class="card-title mb-9 fw-semibold"> Total Debts Payment </h5>
                                                <h4 class="fw-semibold mb-3"><span class="ti ti-currency-peso"></span>{{ $customer_payment_sum }}</h4>
                                                @foreach ($daily_transactions as $daily_transaction)
                                                    <div class="d-flex align-items-center pb-1">
                                                        {{-- <span class="me-2 rounded-circle bg-light-danger round-20 d-flex align-items_center justify-content-center">
                                                            <i class="ti ti-arrow-down-right text-danger"></i>
                                                        </span> --}}
                                                        <p class="text-dark me-1 fs-3 mb-0">
                                                            {{ number_format($percentage_changes[$daily_transaction->date], 2) }}%
                                                        </p>
                                                        <p class="fs-3 mb-0">{{ $daily_transaction->date }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="col-4">
                                                <div class="d-flex justify-content-end">
                                                    <div
                                                        class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-currency-dollar fs-6"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="earning"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-sm-6 col-xl-3">
                            <div class="card overflow-hidden rounded-2">
                                <div class="position-relative">
                                    <a href="javascript:void(0)"><img style="width: 100%; height: 250px !important;" src="{{ asset($product->image) }}" class="card-img-top rounded-0 img-fluid" alt="..."></a>
                                </div>
                                <div class="card-body pt-3 p-4">
                                    <h6 class="fw-semibold fs-4">{{ $product->name }}</h6>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h6 class="fw-semibold fs-4 mb-0"><span class="ti ti-currency-peso"></span> {{ $product->price }} <span class="ms-2 fw-normal text-muted fs-3"><del><span class="ti ti-currency-peso"></span> {{ $product->price  + 59 }}</del></span></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            var user = "{!! $merchant->name !!}";
            var currentTime = new Date().getHours();
            var greetings;

            if (currentTime >= 0 && currentTime < 12) {
                greetings = "Good morning!";
            } else if (currentTime >= 12 && currentTime < 18) {
                greetings = "Good afternoon!";
            } else {
                greetings = "Good evening!";
            }

            iziToast.success({
                title: "Welcome " + user,
                message: greetings,
                position: "topRight",
                transitionIn: "bounceInDown",
                transitionOut: "flipOutX",
            });
        })
        // Pass the PHP variables to JavaScript variables
        const months = {!! json_encode($months) !!};
        const customerCounts = {!! json_encode($customerCounts) !!};
        let products = [];

        @foreach ($products as $product)
            products.push({!! json_encode($product->name) !!});            
        @endforeach

        let debtorName = [];
        let debtorAmount = [];
        
        @foreach ($top_debtors_arr as $top_debtors_key => $top_debtors)
            debtorName.push({!! json_encode($top_debtors_key) !!});
            debtorAmount.push({!! json_encode($top_debtors) !!});
        @endforeach

        const earnings = {!! json_encode($earnings) !!};
    </script>
    <script src="{{ asset('js/merchant_dashboard.js') }}"></script>
@endsection