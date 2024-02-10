@extends('layouts.main_layout')
@section('content')
@section('styles')
@endsection
@include('layouts.admin-sidebar')
<div class="body-wrapper">
    @include('layouts.navbar')

    <div class="container-fluid">
        <div class="row">
            @if (session('success'))
                <div class="alert alert-success" id="sessionAlert">
                    <span class="ti ti-lock-check"></span> {{ session('success') }}
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        Merchants
                        <button class="float-end btn btn-primary btn-sm" id="addMerchantButton"><span
                                class="ti ti-plus"></span> Add</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="merchantTable">
                            <thead>
                                <tr>
                                    <th>Merchant Name</th>
                                    <th>Merchant Username</th>
                                    <th>Merchant Password</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($merchants as $merchant)
                                        <tr>
                                            <td>{{ $merchant['name'] }}</td>
                                            <td>{{ $merchant['email'] }}</td>
                                            <td>{{ $merchant['merchants_no'] }}</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="addMerchantModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add Merchant</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addMerchantForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="merchant_name">Merchant Name</label>
                        <input type="text" class="form-control mt-2 mb-2 merchant_name_error" id="merchant_name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="merchantSubmitButton" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="merchantInfoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="merchantInfoData">

        </div>
    </div>
</div>

<div class="modal fade" id="restrictionConfirmationModal" tabindex="-1" aria-labelledby="restriction-modal"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Restrict Confirmation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="{{ route('restrict.account') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="action" id="action" value="{{ csrf_token() }}">
                    <input type="hidden" name="user_id" id="user_id" value="{{ csrf_token() }}">
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <label for="resttricted-days" class="form-label">Restricted Days</label>
                            <input type="date" name="restricted_days" id="restricted_days"
                                class="form-control datepicker" required>
                        </div>

                        <div class="col-lg-6">
                            <label for="restricted_count" class="form-label">--</label>
                            <input type="text" value="" id="restricted_days_count" class="form-control"
                                readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea name="reason" id="reason" cols="30" rows="4" class="form-control" required></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 justify-content-start">
                            <button type="submit" class="btn btn-success"><span class="ti ti-send"></span>
                                Confirm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    var merchantsData;
    $(document).ready(function() {

        merchantsData = $("#merchantTable").DataTable({
            responsive: true,
            ajax: {
                url: "{{ route('admin.store_merchant_data') }}",
                type: "GET",
                data: function() {

                },
                dataSrc: "",
            },
            columns: [{
                    data: "merchant_name"
                },
                {
                    data: "merchant_email"
                },
                {
                    data: "merchant_password"
                },
                {
                    data: "action"
                },
            ]
        });

        $("#addMerchantButton").click(function(e) {
            e.preventDefault();
            $("#addMerchantModal").modal('show');
            $("#addMerchantForm").submit(function(e) {
                e.preventDefault();
                var formData = new FormData;
                formData.append('merchant_name', $("#merchant_name").val());
                formData.append('store_name', $("#store_name").val());
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('admin.store_merchants') }}",
                    type: "POST",
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        $("#merchantSubmitButton").prop('disabled', true);
                        localStorage.setItem('storeMerchant', response.message);
                        location.reload();
                    },
                    error: function(response) {
                        $("merchantSubmitButton").prop('disabled', false);
                        $(".merchant_name_error").css("border", "1px solid red")
                            .attr('placeholder', response.responseJSON
                                .merchant_name);
                        $(".store_name_error").css("border", "1px solid red").attr(
                            'placeholder', response.responseJSON.store_name);
                    },
                });
            });
        });
    });
    var storeMerchant = localStorage.getItem('storeMerchant');

    if (storeMerchant) {
        iziToast.success({
            title: "Success",
            message: storeMerchant,
            position: "topRight",
            transitionIn: "bounceInDown",
            transitionOut: "flipOutX",
        });

        localStorage.removeItem('storeMerchant');
    }

    $(document).on("click", "#activeMerchant", function() {
        var merchantStatus = $(this).data("is_active");
        var userId = $(this).data("user_id");

        $.ajax({
            url: "{{ route('admin.store_merchant_update_status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                merchant_status: merchantStatus,
                user_id: userId,
            },
            success: function(response) {
                if (response) {
                    merchantsData.ajax.reload()
                }
            }
        });
    });

    $(document).on("click", ".viewMerchant", function() {
        var merchantId = $(this).data("merchant_id");

        $.ajax({
            url: "{{ route('admin.store_merchants_info') }}",
            type: "GET",
            data: {
                merchant_id: merchantId,
            },
            success: function(response) {
                if (response) {
                    $("#merchantInfoModal").modal("show");
                    $("#merchantInfoData").html(response);
                }
            },
        });
    });

    $(document).on("submit", "#updateMerchantForm", function(e) {
        e.preventDefault();

        var updateMerchantForm = new FormData(document.getElementById("updateMerchantForm"));

        $.ajax({
            url: "{{ route('admin.store_merchants_update') }}",
            type: "POST",
            data: updateMerchantForm,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {

                    iziToast.success({
                        title: "Success",
                        message: "Personal info upated.",
                        position: "topRight",
                        transitionIn: "bounceInDown",
                        transitionOut: "flipOutX",
                    });

                    $("#merchantInfoModal").modal("hide");

                } else if (response.error) {

                    $.map(response.error, function(value) {
                        iziToast.error({
                            title: "Oops",
                            message: value,
                            position: "topRight",
                            transitionIn: "bounceInDown",
                            transitionOut: "flipOutX",
                        });
                    })
                }
            }
        });
    });

    $(document).on("click", ".restrictMerchant", function() {

        // continue here dugoyd


        var userId = $(this).data("user_id");
        var action = $(this).data("action");

        $("#action").val(action);
        $("#user_id").val(userId);

        if (action == "Unrestricted") {

            $.ajax({
                url: "{{ route('restrict.account') }}",
                type: "POST",
                data: {
                    user_id: userId,
                    action: action,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $("#sessionAlert").hide();
                    merchantsData.ajax.reload();

                    iziToast.success({
                        title: "Success",
                        message: "Account unrestricted.",
                        position: "topRight",
                        transitionIn: "bounceInDown",
                        transitionOut: "flipOutX",
                    })
                }
            });

        } else {
            $("#restrictionConfirmationModal").modal("show");

            const restrictedDays = $("#restricted_days").flatpickr({
                minDate: "today",
                mode: "range",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        // Get the start and end dates from the selectedDates array
                        const startDate = selectedDates[0];
                        const endDate = selectedDates[1];

                        // Calculate the difference in days
                        const timeDifference = endDate.getTime() - startDate.getTime();
                        const daysDifference = Math.ceil(timeDifference / (1000 * 3600 * 24));

                        $("#restricted_days_count").val(daysDifference + " days of restriction.")

                    }
                }
            });
        }

    });
</script>
@endsection
