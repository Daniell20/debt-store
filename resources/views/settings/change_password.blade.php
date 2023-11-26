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
                            <form class="form-horizontal">
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="password" id="newPassword">
                                </div>
                                <div class="mb-4">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="confirmPassword" id="confirmPassword">
                                </div>
                                <button type="button" id="updatePasswordBtn" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2" disabled>Update Password</button>
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
            iziToast.info({
                title: "Hello",
                message: "Please change your password for security purposes only!",
                position: "topRight",
                transitionIn: "bounceInDown",
                transitionOut: "flipOutX",
            });

            const errorMessage = "{{ session('error') }}";
            if (errorMessage) {
                iziToast.error({
                    title: 'Error',
                    message: errorMessage,
                    position: 'topRight',
                    transitionIn: "bounceInDown",
                    transitionOut: "flipOutX",
                });
            }

            $("#confirmPassword").on("input", function () {
                if ($(this).val() == $("#newPassword").val()) {
                    $("#confirmPassword").css("border", "1px solid green");
                    $("#updatePasswordBtn").prop("disabled", false);
                } else {
                    $("#confirmPassword").css("border", "1px solid red");
                    $("#updatePasswordBtn").prop("disabled", true);
                }
            });

            $("#updatePasswordBtn").click(function () {

                if ($("#newPassword").val() == "" || $("#confirmPassword").val() == "") {
                    iziToast.error({
                        title: "Oops",
                        message: "Please innput your password",
                        position: "topRight",
                        transitionIn: "bounceInDown",
                        transitionOut: "flipOutX",
                    });
                } else {
                    const userMerchant = "{!! auth()->user()->is_merchant !!}";
                    const userCxs = "{!! auth()->user()->is_customer !!}";

                    var url;
                    var redirectUrl;
                    if (userMerchant == 1) {
                        url = "{{ route('merchant.update.password') }}";
                        redirectUrl = "{{ route('merchant.dashboard') }}";
                    } else {
                        url = "{{ route('customer.update.password') }}";
                        redirectUrl = "{{ route('users.dashboard') }}";
                    }

                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            password: $("#newPassword").val(),
                            confirm_password: $("#confirmPassword").val(),
                        },
                        success: function (response) {
                            if (response.status == "error") {
                                iziToast.error({
                                    title: "Oopss",
                                    message: "Please input another password!",
                                    position: "topRight",
                                    transitionIn: "bounceInDown",
                                    transitionOut: "flipOutX",
                                });
    
                                $("#confirmPassword").css("border", "1px solid red");
                            } else {
                                window.location.href = redirectUrl;
                            }
                        }
                    });
                }
            });

        })
    </script>
@endsection
