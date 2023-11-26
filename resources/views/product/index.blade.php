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
                <div class="card-header">
                    <div class="card-title">
                        Products
                        <button class="float-end btn btn-primary btn-sm" id="addProductButton"><span class="ti ti-plus"></span> Add</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if ($products->count() > 0)
                            @foreach ($products as $product)
                                <div class="col-md-4 py-3 text-center">
                                    <div class="card">
                                        <img style="width: 100%; height: 250px; cursor: pointer;" src="{{ asset($product->image) }}" class="card-img-top img-fluid openProduct" alt="..." data-id="{{ $product->id }}">
                                        <div class="card-body">
                                            <h5 class="fw-semibold fs-4">{{ $product->name }}</h6>
                                            <div class="text-center">
                                                {{-- <h6 class="fw-semibold fs-4 mb-0"><i class="ti ti-currency-peso"></i> {{ $product->price }}</h6> --}}
                                                <p class="card-text"><span class="ti ti-currency-peso"></span> {{ $product->price }}</p>

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
        <!-- Modal -->
        @include('modals.add-product-modal')
        @include('modals.product-modal')
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $(".product_image").on("change", function (e) {
                var file = e.target.files[0];
                var imagePreview = $(".productImagePreview");

                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.attr("src", e.target.result);
                        imagePreview.css("display", "block");
                    }
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.attr("src", "");
                    imagePreview.css("display", "none");
                }
            });

            $("#addProductButton").click(function() {
                var storeList = "{!! $stores->count() !!}";
                
                if (storeList != 0) {
                    $("#addProductModal").modal('show');
        
                    $("#submitProductModalButton").click(function() {
    
                        $(this).prop("disabled", true);
                        $("#saveStoreLoader").addClass("spinner-border spinner-border-sm");
    
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
                                $("#submitProductModalButton").prop("disabled", false);
                                $("#saveStoreLoader").removeClass("spinner-border spinner-border-sm");
                                
                                let errors = response.responseJSON.errors;
                                $('#product_price_error').text(errors.product_price ? errors.product_price[0] : '');
                                $('#product_name_error').text(errors.product_name ? errors.product_name[0] : '');
                                $('#product_description_error').text(errors.product_description ? errors.product_description[0] : '');
                                $('#product_image_error').text(errors.product_image ? errors.product_image[0] : '');
                            }
                        })
                    });
                } else {
                    iziToast.error({
                        title: "Opps",
                        message: "Please check if you created a store!",
                        transitionIn: "bounceInDown",
                        transitionOut: "flipOutX",
                        position: "topRight",
                    });
                }
                
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
                            var logo = "{{ asset('') }}" + dataValue.image;
    
                            $("#edit_product_name").val(dataValue.name);
                            $("#edit_product_price").val(dataValue.price);
                            $("#edit_product_description").val(dataValue.description);
                            $("#edit_product_id").val(dataValue.id);
                            $('.productImagePreview').attr('src', logo).css('display', 'block');
                            $("#productModal").modal('show');
                        });
                    }
                })
            });
    
            $("#updateProduct").click(function() {
                let formData = new FormData(document.getElementById("editProductForm"));
                    formData.append('id', $("#edit_product_id").val());
                    formData.append('product_name', $("#edit_product_name").val());
                    formData.append('product_price', $("#edit_product_price").val());
                    formData.append('product_description', $("#edit_product_description").val());
                    formData.append('product_image', $('.product_image')[0].files[0]);
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
                    error: function(response) {
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
        });
    </script>
@endsection