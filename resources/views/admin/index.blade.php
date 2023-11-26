@extends('layouts.main_layout')
@section('content')
    @section('styles')
    @endsection
    @include('layouts.admin-sidebar')
    <div class="body-wrapper">
        @include('layouts.navbar')
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="w-100">
                        <div class="row">
                            <div class="col-sm-6 d-flex align-items-strech">
                                <div class="card w-100">
                                    <div class="card-body">
                                        <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                                            <div class="mb-3 mb-sm-0">
                                              <h5 class="card-title fw-semibold">Users Overview</h5>
                                            </div>
                                        </div>
                                        <div id="userChartOverview" style="min-height: 360px;"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">Merchants</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <span class="ti ti-building-store" style="font-size: 30px;"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h1 class="mt-1 mb-3">{{ $merchants }}</h1>
                                        <div class="mb-0">
                                            <?php
                                                if ($merchants_percentage < 50) {
                                                    $icon = "ti ti-arrow-down-left";
                                                    $text = "text-danger";
                                                    $background = "bg-light-danger";
                                                } elseif ($merchants_percentage >= 50) {
                                                    $icon = "ti ti-arrow-up-right";
                                                    $text = "text-success";
                                                    $background = "bg-light-success";
                                                } elseif ($merchants_percentage == 50) {
                                                    $icon = "ti ti-scale";
                                                    $text = "text-warning";
                                                    $background = "bg-light-warning";
                                                }
                                            ?>
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="me-1 rounded-circle {{ $background }} round-20 d-flex align-items-center justify-content-center">
                                                    <span class="ti {{ $icon . " " . $text }}"></span>
                                                </span>
                                                <p class="text-dark me-1 fs-3 mb-0">{{ number_format($merchants_percentage, 2) }} %</p>
                                                <p class="fs-3 mb-0">Today</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col mt-0">
                                                <h5 class="card-title">Customers</h5>
                                            </div>

                                            <div class="col-auto">
                                                <div class="stat text-primary">
                                                    <span class="ti ti-users" style="font-size: 30px;"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h1 class="mt-1 mb-3">{{ $customers }}</h1>
                                        <div class="mb-0">
                                            <?php
                                                if ($customers_percentage < 50) {
                                                    $icon = "ti ti-arrow-down-left";
                                                    $text = "text-danger";
                                                    $background = "bg-light-danger";
                                                } elseif ($customers_percentage >= 50) {
                                                    $icon = "ti ti-arrow-up-right";
                                                    $text = "text-success";
                                                    $background = "bg-light-success";
                                                } elseif ($customers_percentage == 50) {
                                                    $icon = "ti ti-scale";
                                                    $text = "text-warning";
                                                    $background = "bg-light-warning";
                                                }
                                            ?>
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="me-1 rounded-circle {{ $background }} round-20 d-flex align-items-center justify-content-center">
                                                    <span class="ti {{ $icon . " " . $text }}"></span>
                                                </span>
                                                <p class="text-dark me-1 fs-3 mb-0">{{ number_format($customers_percentage, 2) }} %</p>
                                                <p class="fs-3 mb-0">Today</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-sm-12 d-flex align-items-strech">
                                <div class="card w-100">
                                    <div class="card-body">
                                        <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
                                            <div class="mb-3 mb-sm-0">
                                              <h5 class="card-title fw-semibold">History</h5>
                                            </div>
                                        </div>
                                        <div class="col-12 table-responsive">
                                            <table class="table table-striped" id="merchantHistoryTable">
                                                <thead>
                                                    <tr>
                                                        <th>Date & Time</th>
                                                        <th>Event Type</th>
                                                        <th>User</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
            
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var merchantHistoryTable;
        $(document).ready(function () {
            merchantHistoryTable = $("#merchantHistoryTable").DataTable({
                order: [[0, 'desc']],
                ajax: {
                    url: "{{ route('admin.history') }}",
                    type: "GET",
                    data: function (data) {

                    },
                    dataSrc: "",
                },
                columns: [
                    {data: "date_time"},
                    {data: "event_type"},
                    {data: "user"},
                    {data: "status"},
                ],
            });
        });
        const users = {!! json_encode($users) !!};
        const customersToday = {!! json_encode($customers_today) !!};
        const merchantsToday = {!! json_encode($merchants_today) !!};
        const inactiveMerchants = {!! json_encode($inactive_merchants) !!};
        const inactiveCustomers = {!! json_encode($inactive_customers) !!};
    </script>
    <script src="{{ asset("js/admin_dashboard.js") }}"></script>
@endsection