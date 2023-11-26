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
                        <div class="card-title">
                            Merchants
                            <button class="float-end btn btn-primary btn-sm" id="addMerchantButton"><span class="ti ti-plus"></span> Add</button>
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
@endsection
@section('scripts')
<script>
    var merchantsData;
    $(document).ready(function() {

        merchantsData = $("#merchantTable").DataTable({
            ajax: {
                url: "{{ route('admin.store_merchant_data') }}",
                type: "GET",
                data: function () {

                },
                dataSrc: "",
            },
            columns: [
                { data: "merchant_name" },
                { data: "merchant_email" },
                { data: "merchant_password" },
                { data: "action" },
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

    $(document).on("click", "#activeMerchant", function () {
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
            success: function (response) {
                if (response) {
                    merchantsData.ajax.reload()
                }
            }
        });
    });

    $(document).on("click", ".viewMerchant", function () {
        var merchantId = $(this).data("merchant_id");

        $.ajax({
            url: "{{ route('admin.store_merchants_info') }}",
            type: "GET",
            data: {
                merchant_id: merchantId,
            },
            success: function (response) {
                if (response) {
                    $("#merchantInfoModal").modal("show");
                    $("#merchantInfoData").html(response);
                }
            },
        });
    });

    $(document).on("submit", "#updateMerchantForm", function (e) {
        e.preventDefault();

        var updateMerchantForm = new FormData(document.getElementById("updateMerchantForm"));

        $.ajax({
            url: "{{ route('admin.store_merchants_update') }}",
            type: "POST",
            data: updateMerchantForm,
            contentType: false,
            processData: false,
            success: function (response) {
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

                    $.map(response.error, function (value) {
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
</script>
@endsection
