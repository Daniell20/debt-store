@extends('layouts.main_layout')
@section('styles')
    <style>
        .product-image {
            width: 100%; /* or set a specific width, e.g., 300px */
            height: 300px; /* set a fixed height */
            object-fit: cover; /* scale the image to cover the area while maintaining aspect ratio */
        }
    </style>
@endsection
@section('content')
    @include('layouts.sidebar')
    <div class="body-wrapper">
        @include('layouts.navbar')
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-5">
                        <div class="col-md-12 col-xs-2">
                            <h5 class="card-title fw-semibold">
                                Products
                                <button class="float-end btn btn-primary bt-sm" id="addProductButton"><span class="ti ti-shopping-cart-plus"></span> Add</button>
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($products as $product)
                            <div class="col-sm-6 col-xl-3">
                                <div class="card overflow-hidden rounded-2">
                                    <div class="position-relative">
                                        <a href="javascript:void(0)"><img src="{{ asset($product->image) }}" class="card-img-top rounded-0 product-image openProduct" alt="..." data-id="{{ $product->id }}"></a>
                                        {{-- <a href="javascript:void(0)" class="bg-primary rounded-circle p-2 text-white d-inline-flex position-absolute bottom-0 end-0 mb-n3 me-3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add To Cart"><i class="ti ti-basket fs-4"></i></a> --}}
                                    </div>
                                    <div class="card-body pt-3 p-4">
                                        <h6 class="fw-semibold fs-4">{{ $product->name }}</h6>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="fw-semibold fs-4 mb-0"><i class="ti ti-currency-peso"></i> {{ $product->price }}</h6>
                                            {{-- <h6 class="fw-semibold fs-4 mb-0">{{ $product['price'] }} <span class="ms-2 fw-normal text-muted fs-3"><del>$345</del></span></h6> --}}
                                            {{-- <ul class="list-unstyled d-flex align-items-center mb-0">
                                                <li><a class="me-1" href="javascript:void(0)"><i class="ti ti-star text-warning"></i></a></li>
                                                <li><a class="me-1" href="javascript:void(0)"><i class="ti ti-star text-warning"></i></a></li>
                                                <li><a class="me-1" href="javascript:void(0)"><i class="ti ti-star text-warning"></i></a></li>
                                                <li><a class="me-1" href="javascript:void(0)"><i class="ti ti-star text-warning"></i></a></li>
                                                <li><a class="" href="javascript:void(0)"><i class="ti ti-star text-warning"></i></a></li>
                                            </ul> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        @include('modals.add-product-modal')
        @include('modals.product-modal')
    </div>
@endsection
@section('scripts')
    <script>
        $("#addProductButton").click(function() {
            $("#addProductModal").modal('show');

            $("#submitProductModalButton").click(function() {

                let formData = new FormData();
                formData.append('product_name', $("#product_name").val());
                formData.append('product_price', $("#product_price").val());
                formData.append('product_description', $("#product_description").val());
                formData.append('product_image', $('#product_image')[0].files[0]);
                formData.append('store_id', $('#store_id').val());
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('merchant.product.save') }}",
                    type: "POST",
                    data: formData,
                    processData: false, // Important: Do not process data
                    contentType: false, // Important: Do not set contentType
                    success: function(response) {
                        localStorage.setItem("storeProduct", response.success);
                        location.reload();
                    },
                    error: function(response) {
                        let errors = response.responseJSON.errors;
                        $('#product_price_error').text(errors.product_price ? errors.product_price[0] : '');
                        $('#product_name_error').text(errors.product_name ? errors.product_name[0] : '');
                        $('#product_description_error').text(errors.product_description ? errors.product_description[0] : '');
                        $('#product_image_error').text(errors.product_image ? errors.product_image[0] : '');
                    }
                })
            });
        });
        var storeProduct = localStorage.getItem("storeProduct");

        if (storeProduct) {
            iziToast.success({
                title: "Success!",
                message: storeProduct,
                position: "topRight",
                transitionIn: "bounceInDown",
                transitionOut: "flipOutX",
            });

            localStorage.removeItem("storeProduct");
        }


        $(".openProduct").click(function() {
            const id = $(this).data('id');
            $.ajax({
                url: "{{ route('merchant.product.get') }}",
                type: "GET",
                data: {
                    id: id
                },
                success: function(response) {
                    $.each(response.data, function(dataIndex, dataValue) {
                        console.log(dataValue.name)
                        $('#current_image_name').text(dataValue.image).removeClass('d-none');
                        $("#edit_product_name").val(dataValue.name);
                        $("#edit_product_price").val(dataValue.price);
                        $("#edit_product_description").val(dataValue.description);
                        $("#edit_product_id").val(dataValue.id);
                        $("#productModal").modal('show');
                    });
                }
            })
        });
        $("#updateProduct").click(function() {
            let formData = new FormData();
            formData.append('id', $("#edit_product_id").val());
            formData.append('product_name', $("#edit_product_name").val());
            formData.append('product_price', $("#edit_product_price").val());
            formData.append('product_description', $("#edit_product_description").val());
            formData.append('product_image', $('#edit_product_image')[0].files[0]);
            formData.append('_token', "{{ csrf_token() }}");

            $.ajax({
                url: "{{ route('merchant.product.update') }}",
                type: "POST",
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    localStorage.setItem("updateProduct", response.success);
                    location.reload();
                },
                errors: function(response) {
                    let errors = response.responseJSON.errors;
                    $('#product_price_error').text(errors.product_price ? errors.product_price[0] : '');
                    $('#product_name_error').text(errors.product_name ? errors.product_name[0] : '');
                    $('#product_description_error').text(errors.product_description ? errors.product_description[0] : '');
                    $('#product_image_error').text(errors.product_image ? errors.product_image[0] : '');
                }
            })
        });

        var updateProduct = localStorage.getItem("updateProduct");

        if (updateProduct) {
            iziToast.success({
                title: "Success!",
                message: updateProduct,
                position: "topRight",
                transitionIn: "bounceInDown",
                transitionOut: "flipOutX",
            });

            localStorage.removeItem("updateProduct");
        }

        $("#deleteProductButton").click(function() {
            const id = $("#edit_product_id").val();
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this product?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',

                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('merchant.product.delete') }}",
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
                                    text: 'Product deleted successfully!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
    </script>
@endsection