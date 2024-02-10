@extends('layouts.main_layout')
@section('content')
    @include('layouts.sidebar')
    <!--  Main wrapper -->
    <div class="body-wrapper">
        @include('layouts.navbar')
        <!--  Header End -->
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Loan Setup
                        <button class="float-end btn btn-primary btn-sm" id="createLoanSetupButton" data-bs-toggle="modal"
                            data-bs-target="#loanSetupModal"><span class="ti ti-plus"></span> Create</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-lg-12">
                        <table class="table table-striped" id="loanSetupTable">
                            <thead>
                                <tr>
                                    <th>Interest Rate</th>
                                    <th>Months To Pay</th>
                                    <th>Action</th>
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

    {{-- Modal --}}

    <div class="modal fade modal-lg" id="loanSetupModal">
        <div class="modal-dialog">
            <div class="modal-content" id="formData">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Create Loan Setup For Your Store</h4>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <form class="form-group" id="loanSetupForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="interest_rate" name="interest_rate"
                                        placeholder="5%">
                                    <label for="interest-rate">Interest Rate (%)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="months_to_pay" name="months_to_pay"
                                        placeholder="5 mos">
                                    <label for="months-to-pay">Months To Pay</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" id="saveLoanSetupButton" class="btn btn-success loanSetupButton"><span
                            class="ti ti-plus"></span> Save</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var loanSetupTable;
        $(document).ready(function() {
            loanSetupTable = $("#loanSetupTable").DataTable({
                responsive: true,
                ajax: {
                    url: "{{ route('loan.show_data') }}",
                    type: "GET",
                    data: function(data) {

                    },
                    dataSrc: "",
                },
                columns: [{
                        data: "interest_rate"
                    },
                    {
                        data: "months_to_pay"
                    },
                    {
                        data: "action"
                    },
                ],
            });


            // create loan setup
            $("#saveLoanSetupButton").click(function() {

                var interestRate = $("#interest_rate").val();
                var monthsToPay = $("#months_to_pay").val();

                if (interestRate, monthsToPay == "") {
                    errorResponse()
                } else {
                    $(this).prop("disabled", true);

                    var loanSetupFormData = new FormData(document.getElementById("loanSetupForm"));
                    loanSetupFormData.append("interest_rate", interestRate);
                    loanSetupFormData.append("months_to_pay", monthsToPay);
                    loanSetupFormData.append("_token", "{{ csrf_token() }}");

                    $.ajax({
                        url: "{{ route('loan.create_interest_setup') }}",
                        type: "POST",
                        data: loanSetupFormData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response) {
                                loanSetupTable.ajax.reload();
                                $("#saveLoanSetupButton").prop("disabled", false);
                                $("#loanSetupModal").modal("hide");
                                $('#loanSetupForm')[0].reset();
                                successResponse()
                            } else {
                                errorResponse()
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
                }
            });

        });

        $(document).on("click", ".editLoanSettingsButton", function() {

            var loanSettingsId = $(this).data("loan_settings_id");

            $.ajax({
                url: "{{ route('loan.edit_data') }}",
                type: "GET",
                data: {
                    loan_settings_id: loanSettingsId,
                },
                success: function(response) {
                    if (response) {
                        $("#loanSetupModal").modal("show");
                        $("#loanSetupForm").hide();
                        $("#formData").html(response);
                    }
                },
            });
        });

        // update loan setup
        $(document).on("click", "#updateLoanSetupButton", function() {

            var loanSettingsId = $("#loan_settings_id").val();
            var interestRate = $("#interest_rate").val();
            var monthsToPay = $("#months_to_pay").val();

            var loanSetupUpdateForm = new FormData(document.getElementById("loanSetupUpdateForm"));
            loanSetupUpdateForm.append("loan_settings_id", loanSettingsId);
            loanSetupUpdateForm.append("interest_rate", interestRate);
            loanSetupUpdateForm.append("months_to_pay", monthsToPay);
            loanSetupUpdateForm.append("_token", "{{ csrf_token() }}");

            $.ajax({
                url: "{{ route('loan.update_data') }}",
                type: "POST",
                data: loanSetupUpdateForm,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response) {
                        successResponse();
                        loanSetupTable.ajax.reload();
                        $("#loanSetupModal").modal("hide");
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

        // delete loan setup

        $(document).on("click", ".deleteLoanSettingsButton", function() {
            var loanSettingsId = $(this).data("loan_settings_id");

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
                            url: "{{ route('loan.delete_data') }}",
                            type: "POST",
                            data: {
                                loan_settings_id: loanSettingsId,
                                _token: "{{ csrf_token() }}",
                            },
                            success: function(response) {
                                if (response) {
                                    iziToast.success({
                                        title: "Success",
                                        message: "Data deleted.",
                                        position: "topRight",
                                        transitionIn: "bounceInDown",
                                        transitionOut: "flipOutX",
                                    });
                                    instance.hide({
                                        transitionOut: 'fadeOut'
                                    }, toast, 'button');
                                    loanSetupTable.ajax.reload();
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
    </script>
    <script>
        function errorResponse() {
            iziToast.error({
                title: "Oops",
                message: "Something went wrong please try again!",
                position: "topRight",
                transitionIn: "bounceInDown",
                transitionOut: "flipOutX",
            });
        }

        function successResponse() {
            iziToast.success({
                title: "Success",
                message: "Data saved.",
                position: "topRight",
                transitionIn: "bounceInDown",
                transitionOut: "flipOutX",
            });
        }
    </script>
@endsection
