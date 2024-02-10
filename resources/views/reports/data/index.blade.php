@extends('layouts.main_layout')
@section('content')
@section('styles')
@endsection
@include('layouts.admin-sidebar')
<div class="body-wrapper">
    @include('layouts.navbar')

    <div class="container-fluid">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-semibold card-title mb-4">
                        Reports
                    </h5>

                    <div class="card">
                        <div class="card-body table-responsive">
                            <table class="table table-striped" id="reportsTable">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Merchant</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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
    var reportsTable;
    $(document).ready(function() {
        reportsTable = $("#reportsTable").DataTable({
            responsive: true,
            ajax: {
                url: "{{ route('reports.data') }}",
                type: "GET",
                dataSrc: "",
            },
            columns: [{
                    data: "customer"
                },
                {
                    data: "merchant"
                },
                {
                    data: "description"
                },
            ]
        });
    });
</script>
@endsection
