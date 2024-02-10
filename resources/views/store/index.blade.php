@extends('layouts.main_layout')
@section('styles')
    <style>
        .store-image {
            width: 100%;
            /* or set a specific width, e.g., 300px */
            height: 300px;
            /* set a fixed height */
            object-fit: cover;
            /* scale the image to cover the area while maintaining aspect ratio */
        }
    </style>
@endsection

@section('content')
    @include('layouts.sidebar')
    <div class="body-wrapper">
        @include('layouts.navbar')
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Stores
                        <button class="float-end btn btn-primary btn-sm" id="addStore"><span class="ti ti-plus"></span>
                            Add</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if ($stores->count() > 0)
                            @foreach ($stores as $store)
                                <div class="col-md-4 py-3 text-center">
                                    <div class="card">
                                        <img style="width: 100%; height: 250px; cursor: pointer;"
                                            src="{{ asset($store->logo) }}" class="card-img-top img-fluid openStore"
                                            alt="..." data-store_id="{{ $store->id }}">
                                        <div class="card-body">
                                            <h5 class="fw-semibold fs-4">{{ $store->name }}</h5>
                                            <div class="text-center">
                                                {{-- <h6 class="fw-semibold fs-4 mb-0"><i class="ti ti-currency-peso"></i> {{ $store->address }}</h6> --}}
                                                <p class="card-text">{{ $store->address }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="cold-md-4 py-3 text-center">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="fw-semibold fs-4">No Data Available</h5>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modals.add-store-modal')
    @include('modals.store-modal')
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {

            $('.store_logo').on('change', function(e) {
                var file = e.target.files[0];
                var imagePreview = $('.imagePreview');

                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.css('display', 'block'); // Show the image element
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.attr('src', ''); // Clear the image source
                    imagePreview.css('display', 'none'); // Hide the image element
                }
            });

            // creating store
            $("#addStore").click(function() {
                $("#addStoreModal").modal('show');

                $("#saveStore").click(function() {
                    $(this).prop("disabled", true);
                    $("#saveStoreLoader").addClass("spinner-border spinner-border-sm");

                    var formData = new FormData(document.getElementById("addStoreForm"));
                    formData.append('store_name', $("#store_name").val());
                    formData.append('store_address', $("#store_address").val());
                    formData.append('store_phone', $("#store_phone").val());
                    formData.append('store_email', $("#store_email").val());
                    formData.append('store_logo', $("#store_logo")[0].files[0]);
                    formData.append('_token', "{{ csrf_token() }}");

                    $.ajax({
                        url: "{{ route('merchant.store.save') }}",
                        type: "POST",
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function(response) {
                            localStorage.setItem('storeSuccess', response.message);
                            location.reload();
                        },
                        error: function(response) {
                            iziToast.error({
                                title: "Oops",
                                message: response.responseJSON.error,
                                position: "topRight",
                                transitionIn: "bounceInDown",
                                transitionOut: "flipOutX",
                            })
                            if (response) {
                                $("#saveStore").prop("disabled", false);
                                $("#saveStoreLoader").removeClass(
                                    "spinner-border spinner-border-sm");

                                const error = response.responseJSON.error_message;
                                $("#store_name_error").text(error.store_name ? error
                                    .store_name[0] : '');
                                $("#store_address_error").text(error.store_address ?
                                    error.store_address[0] : '');
                                $("#store_phone_error").text(error.store_phone ? error
                                    .store_phone[0] : '');
                                $("#store_email_error").text(error.store_email ? error
                                    .store_email[0] : '');
                                $("#store_logo_error").text(error.store_logo ? error
                                    .store_logo[0] : '');
                            }
                        }

                    });
                });
            });

            $(".openStore").on("click", function() {
                var storeId = $(this).data("store_id");

                $.ajax({
                    url: "{{ URL::to('merchant/get-store') }}",
                    type: "GET",
                    data: {
                        store_id: storeId,
                    },
                    success: function(response) {
                        $.each(response.data, function(dataIndex, dataValue) {

                            var logo = "{{ asset('') }}" + dataValue.logo;

                            $('#current_image_name').text(dataValue.logo).removeClass(
                                'd-none');
                            $("#edit_store_name").val(dataValue.name);
                            $("#edit_store_address").val(dataValue.address);
                            $("#edit_store_phone").val(dataValue.phone);
                            $("#edit_store_id").val(dataValue.id);
                            $('.imagePreview').attr('src', logo).css('display',
                                'block');

                            $("#storeModal").modal('show');
                        });
                    },
                });
            });

            $('#storeModal, #addStoreModal').modal({
                backdrop: 'static',
                keyboard: false,
            })

            $('#storeModal').on('hidden.bs.modal', function() {
                window.location.reload();
            });

            var storeSuccess = localStorage.getItem('storeSuccess');
            if (storeSuccess) {
                iziToast.success({
                    title: "Success!",
                    message: storeSuccess,
                    position: "topRight",
                    transitionIn: "bounceInDown",
                    transitionOut: "flipOutX",
                });
                localStorage.removeItem("storeSuccess");
            }

            // update store
            $("#updateStore").click(function() {
                let editStoreFormData = new FormData(document.getElementById("editStoreForm"));
                console.log(editStoreFormData);
                editStoreFormData.append('id', $("#edit_store_id").val());
                editStoreFormData.append('store_name', $("#edit_store_name").val());
                editStoreFormData.append('store_address', $("#edit_store_address").val());
                editStoreFormData.append('store_phone', $("#edit_store_phone").val());
                editStoreFormData.append('store_image', $('#store_logo')[0].files[0]);
                editStoreFormData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('merchant.store.update') }}",
                    type: "POST",
                    contentType: false,
                    processData: false,
                    data: editStoreFormData,
                    success: function(response) {
                        localStorage.setItem("updateStore", response.success);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 403) {
                            iziToast.error({
                                title: "Oops",
                                message: xhr.responseJSON.error,
                                position: "topRight",
                                transitionIn: "bounceInDown",
                                transitionOut: "flipOutX",
                            })
                        }
                        if (xhr.status == 422) {
                            var errors = xhr.responseJSON.errors;
                            $('#store_phone_error').text(errors.store_phone ? errors
                                .store_phone[0] : '');
                            $('#store_name_error').text(errors.store_name ? errors.store_name[
                                0] : '');
                            $('#store_address_error').text(errors.store_address ? errors
                                .store_address[0] : '');
                            $('#store_image_error').text(errors.store_image ? errors
                                .store_image[0] : '');
                        }
                    }
                })
            });

            var updateStore = localStorage.getItem("updateStore");

            if (updateStore) {
                iziToast.success({
                    title: "Success!",
                    message: updateStore,
                    position: "topRight",
                    transitionIn: "bounceInDown",
                    transitionOut: "flipOutX",
                });

                localStorage.removeItem("updateStore");
            }

            // delete store
            $("#deleteStoreButton").click(function() {
                const id = $("#edit_store_id").val();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this store?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',

                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('merchant.store.delete') }}",
                            type: "POST",
                            data: {
                                id: id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: 'Store deleted successfully!'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            location.reload();
                                        }
                                    })
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status == 403) {
                                    iziToast.error({
                                        title: "Oops",
                                        message: xhr.responseJSON.error,
                                        position: "topRight",
                                        transitionIn: "bounceInDown",
                                        transitionOut: "flipOutX",
                                    })
                                }
                            }
                        })
                    }
                })
            })
        })
    </script>
@endsection
