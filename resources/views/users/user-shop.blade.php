@extends('layouts.main_layout')
@section('content')
@include('layouts.users.sidebar')
    <div class="body-wrapper">
        @include('layouts.navbar')
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title"> Available Products </h5>
                        </div>
                        <div class="card-body">
                            @if ($products->count() > 0)
                                <div class="row">
                                    @foreach ($products as $product)
                                        <div class="col-sm-6 col-xl-3">
                                            <div class="card overflow-hidden rounded-2">
                                                <div class="position-relative">
                                                    <a href="{{ asset($product['image']) }}" class="popup-link"><img src="{{ asset($product['image']) }}" class="card-img-top rounded-0 img-fluid" style="height: 200px;" alt="..."></a>
                                                    <a type="button" class="bg-primary rounded-circle p-2 text-white d-inline-flex position-absolute bottom-0 end-0 mb-n3 me-3 loanProductButton" data-store_id="{{ $product["store_id"] }}" data-product_id="{{ $product["product_id"] }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add To Cart"><i class="ti ti-basket fs-4"></i></a>
                                                </div>
                                                <div class="card-body pt-3 p-4">
                                                    <h6 class="fw-semibold fs-4">{{ $product["name"] }}</h6>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <?php
                                                            $discount = $product["price"] + 100; // sample discount
                                                        ?>
                                                        <h6 class="fw-semibold fs-4 mb-0"><span class="ti ti-currency-peso"></span> {{ $product["price"] }} <span class="ms-2 fw-normal text-muted fs-3"><span class="ti ti-currency-peso"></span><del> {{ $discount }}</del></span></h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="col-lg-12">
                                    <div class="alert alert-info text-center">No products are available. Please check your credit limit or contact the store admin.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal --}}
        <div class="modal modal-lg fade" id="loanProdcutModal">
            <div class="modal-dialog">
                <div class="modal-content">
            
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <div class="col-lg-12">
                            <h4 class="modal-title alert alert-primary">Loan this product?
                                <span>Due date will be reflected on the details tab.</span>
                            </h4>
                            
                        </div>
                    </div>
            
                    <!-- Modal body -->
                    <div class="modal-body">
                        <div id="data">

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $(".popup-link").magnificPopup({
                type: "image",
            });

            // loan the product
            $(".loanProductButton").click(function () {
                var storeId = $(this).data("store_id");
                var productId = $(this).data("product_id");

                $.ajax({
                    url: "{{ route('users.loan.details') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        store_id: storeId,
                        product_id: productId,
                    },
                    success: function (response) {
                        if (response) {
                            $("#loanProdcutModal").modal("show");
                            $("#data").html(response);
                        }
                    },
                });
            });
            $("#loanProdcutModal").modal({
                backdrop: "static",
                keyboard: false,
            });

            $('#loanProdcutModal').on('hidden.bs.modal', function () {
                location.reload();
            });
        });
    </script>
@endsection