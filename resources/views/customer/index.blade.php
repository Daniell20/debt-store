@extends('layouts.main_layout')
@section('content')
    @include('layouts.sidebar')

    <div class="body-wrapper">

        @include('layouts.navbar')

        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Customer List
                        <button class="float-end btn btn-primary btn-sm" id="addProductButton"><span class="ti ti-plus"></span>
                            Add</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row table-responsive">
                        <table id="customersTable" class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Customer ID</th>
                                    <th>Name</th>
                                    <th>Credit Limit</th>
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
    </div>
    <!-- The Modal -->
    <div class="modal fade modal-lg" id="createCustomerModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Add Customer</h4>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form id="customerForm">
                        <div class="form-floating mb-3 mt-3">
                            <input type="text" class="form-control" id="customer_name" placeholder="Enter Name"
                                name="customer_name">
                            <label for="customer-name">Customer Full Name</label>
                        </div>

                        <div class="form-floating mb-3 mt-3">
                            <input type="text" class="form-control" id="customer_address" placeholder="Enter Name"
                                name="customer_address">
                            <label for="customer-address">Customer Address</label>
                        </div>

                        <div class="form-floating mb-3 mt-3">
                            <input type="number" class="form-control" id="customer_contact_number" placeholder="Enter Name"
                                name="customer_contact_number">
                            <label for="customer-contact-number">Customer Contact Number</label>
                        </div>

                        <div class="form-floating mb-3 mt-3">
                            <input type="email" class="form-control" id="customer_email" placeholder="Enter Name"
                                name="customer_email">
                            <label for="customer-email">Customer Email</label>
                        </div>

                        <div class="form-floating mb-3 mt-3">
                            <input type="number" class="form-control" id="credit_limit" placeholder="Enter Name"
                                name="credit_limit">
                            <label for="credit-limit">Credit Limit</label>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="saveCustomerButton"><span
                            class="ti ti-device-floppy"></span> Save</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><span
                            class="ti ti-square-rounded-x-filled"></span> Close</button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal modal-lg fade" id="customerDetailsModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Customer Details</h4>
                </div>

                <form id="editCustomerForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <!-- Modal body -->
                    <div class="modal-body" id="modalBodyContent">

                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><span class="ti ti-pencil"></span> Update</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><span class="ti ti-x"></span>
                            Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var customersTable;
        $(document).ready(function() {
            var loanSettings = "{!! $loan_settings !!}";

            customersTable = $('#customersTable').DataTable({
                ajax: {
                    url: '{{ route('customer-data.index') }}',
                    type: 'GET',
                    data: function(data) {

                    },
                    dataSrc: "",
                },
                columns: [{
                        data: 'customer_id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'credit_limit'
                    },
                    {
                        data: 'action'
                    },
                ],
            });

            $("#addProductButton").click(function() {
                if (loanSettings == 0) {
                    iziToast.error({
                        title: "Oops",
                        message: "Please go to 'Loan Setup' first.",
                        position: "topRight",
                        transitionIn: "bounceInDown",
                        transitionOut: "flipOutX",
                    });
                } else {
                    $("#createCustomerModal").modal("show");
                }
            });

            // create customer
            $("#saveCustomerButton").click(function() {
                $(this).prop("disabled", true);

                var customerFormData = new FormData(document.getElementById("customerForm"));
                customerFormData.append("customer_name", $("#customer_name").val());
                customerFormData.append("customer_address", $("#customer_address").val());
                customerFormData.append("customer_contact_number", $("#customer_contact_number").val());
                customerFormData.append("customer_email", $("#customer_email").val());
                customerFormData.append("credit_limit", $("#credit_limit").val());
                customerFormData.append("_token", "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('save.customer.details') }}",
                    type: "POST",
                    processData: false,
                    contentType: false,
                    data: customerFormData,
                    success: function(response) {
                        if (response.errors) {
                            $("#saveCustomerButton").prop("disabled", false);

                            iziToast.error({
                                title: "Oops",
                                message: $.map(response.errors, function(value) {
                                    return value
                                }),
                                position: "topRight",
                                transitionIn: "bounceInDown",
                                transitionOut: "flipOutX",
                                timeout: 8000,
                            });
                        } else {
                            customersTable.ajax.reload();
                            document.getElementById("customerForm").reset();

                            $("#createCustomerModal").modal("hide");
                            $("#saveCustomerButton").prop("disabled", false);


                            iziToast.success({
                                title: "Perfect",
                                message: "Customer successfully save!",
                                position: "topRight",
                                transitionIn: "bounceInDown",
                                transitionOut: "flipOutX",
                            });
                        }
                    },
                    error: function(response) {
                        if (response.status == 403) {
                            iziToast.error({
                                title: "Oops",
                                message: response.responseJSON.error,
                                position: "topRight",
                                transitionIn: "bounceInDown",
                                transitionOut: "flipOutX",
                            })
                        }
                    }
                });

            });
        });

        $(document).on("click", ".viewCustomerDetail", function() {
            var customerId = $(this).data("customer_id");

            $.ajax({
                url: "{{ route('merchant.customer.detail') }}",
                type: "GET",
                data: {
                    customer_id: customerId,
                },
                success: function(response) {
                    if (response) {
                        $("#customerDetailsModal").modal("show");
                        $("#modalBodyContent").html(response);
                    }
                }
            });
        });

        // deactivate customer
        $(document).on("click", ".deactivateCustomerButton", function(e) {
            e.preventDefault();

            var userId = $(this).data("user_id");
            var statusId = $(this).data("status_id");

            iziToast.question({
                timeout: 20000,
                close: false,
                overlay: true,
                displayMode: 'once',
                id: 'question',
                zindex: 999,
                title: 'Hey',
                message: 'Are you sure about that?',
                position: 'center',
                buttons: [
                    ['<button><b>YES</b></button>', function(instance, toast) {

                        $.ajax({
                            url: "{{ route('deactivate.customer') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                status_id: statusId,
                                user_id: userId,
                            },
                            success: function(response) {
                                if (response) {
                                    customersTable.ajax.reload();
                                    instance.hide({
                                        transitionOut: 'fadeOut'
                                    }, toast, 'button');

                                    iziToast.success({
                                        title: "Success",
                                        message: "Customer " + response +
                                            " successfully",
                                        position: "topRight",
                                        transitionIn: "bounceInDown",
                                        transitionOut: "flipOutX",
                                        timeout: 2000,
                                    });
                                }
                            },
                            error: function(response) {
                                if (response.status == 403) {
                                    iziToast.error({
                                        title: "Oops",
                                        message: response.responseJSON.error,
                                        position: "topRight",
                                        transitionIn: "bounceInDown",
                                        transitionOut: "flipOutX",
                                    })
                                }
                            }
                        });

                    }, true],
                    ['<button>NO</button>', function(instance, toast) {

                        instance.hide({
                            transitionOut: 'fadeOut'
                        }, toast, 'button');

                    }],
                ],
            });
        });

        // edit customer
        $(document).on("submit", "#editCustomerForm", function(e) {
            e.preventDefault();

            var editCustomerForm = new FormData(document.getElementById("editCustomerForm"));

            $.ajax({
                url: "{{ route('merchant.customer.update_detail') }}",
                type: "POST",
                data: editCustomerForm,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.error) {
                        iziToast.error({
                            title: "Oops",
                            message: $.map(response.error, function(value) {
                                return value
                            }),
                            position: "topRight",
                            transitionIn: "bounceInDown",
                            transitionOut: "flipOutX",
                        });
                    } else if (response.success) {
                        iziToast.success({
                            title: "Success",
                            message: "Customer updated.",
                            position: "topRight",
                            transitionIn: "bounceInDown",
                            transitionOut: "flipOutX",
                        });

                        $("#customerDetailsModal").modal("hide");
                        customersTable.ajax.reload();
                    }
                },
                error: function(response) {
                    if (response.status == 403) {
                        iziToast.error({
                            title: "Oops",
                            message: response.responseJSON.error,
                            position: "topRight",
                            transitionIn: "bounceInDown",
                            transitionOut: "flipOutX",
                        })
                    }
                }
            });
        });
    </script>
@endsection
