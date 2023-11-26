@extends('layouts.main_layout')
@section("styles")
    <style>
        .img-account-profile {
            height: 10rem;
        }
        .rounded-circle {
            border-radius: 50% !important;
        }
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgb(33 40 50 / 15%);
        }
        .card .card-header {
            font-weight: 500;
        }
        .card-header:first-child {
            border-radius: 0.35rem 0.35rem 0 0;
        }
        .card-header {
            padding: 1rem 1.35rem;
            margin-bottom: 0;
            background-color: rgba(33, 40, 50, 0.03);
            border-bottom: 1px solid rgba(33, 40, 50, 0.125);
        }
        .form-control, .dataTable-input {
            display: block;
            width: 100%;
            padding: 0.875rem 1.125rem;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1;
            color: #69707a;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #c5ccd6;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.35rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .nav-borders .nav-link.active {
            color: #0061f2;
            border-bottom-color: #0061f2;
        }
        .nav-borders .nav-link {
            color: #69707a;
            border-bottom-width: 0.125rem;
            border-bottom-style: solid;
            border-bottom-color: transparent;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            padding-left: 0;
            padding-right: 0;
            margin-left: 1rem;
            margin-right: 1rem;
        }
    </style>
@endsection
@section('content')
    @include('layouts.users.sidebar-profile')
    <div class="body-wrapper">
        @include('layouts.navbar')
        
        <div class="container-fluid">
            <form action="" id="userProfileForm" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="user_id" value="{{ $user_detail->user_id }}">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <!-- Profile picture card-->
                        <div class="card mb-4 mb-xl-0">
                            <div class="card-header">Profile Picture</div>
                            <div class="card-body text-center">
                                <!-- Profile picture image-->
                                <img id="previewImage" class="img-account-profile rounded-circle mb-2" src="{{ asset($user_detail->profile_picture) }}" alt="">
    
                                <!-- Profile picture upload button and file input-->
                                <input type="file" class="btn btn-primary" id="fileInput" name="profile_image" style="display: none;">
                                <label for="fileInput" class="btn btn-primary">Upload New Image</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <!-- Account details card-->
                        <div class="card mb-4">
                            <div class="card-header">Account Details</div>
                            <div class="card-body">
                                <!-- Form Group (username)-->
                                <div class="row gx-3 mb-3">

                                    <div class="col-md-6">
                                        <label class="small mb-1" for="username">Username</label>
                                        <input class="form-control" id="username" name="username" type="text" placeholder="Enter your username" value="{{ $user_detail->username }}" disabled>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="small mb-1 d-flex justify-content-between" for="password">
                                            <span>Password</span>
                                            <span class="text-right"><a href="">Change Password?</a></span>
                                        </label>
                                        <input class="form-control" id="password" type="password" placeholder="Enter your username" value="{{ $user_detail->secret }}" disabled>
                                    </div>
                                   
                                </div>
                                <!-- Form Row-->
                                <div class="row gx-3 mb-3">
                                    <!-- Form Group (first name)-->
                                    <div class="col-md-12">
                                        <label class="small mb-1" for="name">Name</label>
                                        <input class="form-control" name="name" id="name" type="text" placeholder="Enter your first name" value="{{ $user_detail->name }}">
                                    </div>
                                </div>
                                <!-- Form Row        -->
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="address">Location</label>
                                        <input class="form-control" id="address" name="address" type="text" placeholder="Enter your location" value="{{ $user_detail->address }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="small mb-1" for="contact_number">Phone number</label>
                                        <input class="form-control" id="contact_number" name="contact_number" type="tel" placeholder="Enter your phone number" value="{{ $user_detail->contact_number }}">
                                    </div>
                                </div>
                                <!-- Form Group (email address)-->
                                <div class="mb-3">
                                    <label class="small mb-1" for="email">Email address</label>
                                    <input class="form-control" id="email" type="email" name="email" placeholder="Enter your email address" value="{{ $user_detail->email }}">
                                </div>
                                <!-- Save changes button-->
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
	</div>
@endsection
@section("scripts")
    <script>
        const fileInput = document.getElementById("fileInput");
        const uploadLabel = document.getElementById("uploadLabel");

        // Add an event listener to the file input
        fileInput.addEventListener("change", function () {
            const previewImage = document.getElementById("previewImage");
            const file = this.files[0];

            if (file) {
                // Check if the selected file is an image
                if (file.type.match("image.*")) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        // Set the source of the img element to the image data
                        previewImage.src = e.target.result;
                    };

                    // Read the selected file as a data URL
                    reader.readAsDataURL(file);
                } else {
                    iziToast.error({
                        title: "Oops",
                        message: "File must be an image.",
                        position: "topRight",
                        transitionIn: "bounceInDown",
                        transitionOut: "flipOutX",
                    });
                }
            }
        });

        const customer = "{{ Auth::user()->is_customer }}";
        if (customer == 1) {
            var route = "{!! url('customer/users-update_profile') !!}";
        } else {
            var route = "{!! url('merchant/users-update_profile') !!}";
        }

        $(document).ready(function () {
            $("#userProfileForm").on("submit", function (e) {
                e.preventDefault();

                var userProfileForm = new FormData(document.getElementById("userProfileForm"));

                $.ajax({
                    url: route,
                    type: "POST",
                    data: userProfileForm,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.error) {
                            iziToast.error({
                                title: "Oops",
                                message: $.map(response.error, function (value) {
                                    return value
                                }),
                                position: "topRight",
                                transitionIn: "bounceInDown",
                                transitionOut: "flipOutX",
                            });
                        } else if (response.success) {
                            iziToast.success({
                                title: "Success",
                                message: "Profile updated. Please wait to reload the page.",
                                position: "topRight",
                                transitionIn: "bounceInDown",
                                transitionOut: "flipOutX",
                                onClosed: function () {
                                    location.reload();
                                }
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection