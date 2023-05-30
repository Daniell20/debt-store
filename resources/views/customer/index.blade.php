@extends('layouts.main_layout')
@section('content')
    @include('layouts.sidebar')

    <div class="body-wrapper">

        @include('layouts.navbar')

        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title fw-semibold mb-4">Customer List</h5>
                        </div>
                    </div>
                    <table id="my-datatable" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Your table data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#my-datatable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ route("customer-data.index") }}',
                    type: 'GET'
                },
                responsive: true,
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'customer_id', name: 'customers_id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'status', name: 'status'},
                    {
                        data: null,
                        name: 'actions',
                        render: function(data, type, row) {
                            console.log(data)
                            return `
                                <a href="{{ url('view-profile/${data.id}') }}" class="btn btn-info btn-sm viewProfile"><span class="ti ti-eye"></span> View</a>
                                <a href="{{ url('edit-profile/${data.id}') }}" class="btn btn-success btn-sm"><span class="ti ti-edit"></span> Edit</a>
                            `;
                        },
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endsection
