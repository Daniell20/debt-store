@extends('layouts.main_layout')
@section('styles')
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
                        <button class="float-end btn btn-primary btn-sm" id="addStore"><span class="ti ti-plus"></span> Add</button>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        @foreach ($stores as $store)
                            <div class="col-md-4 py-3">
                                <div class="card">
                                    <img style="width: 100%; height: 200px;"  src="{{ asset($store->logo) }}" class="card-img-top img-fluid" alt="...">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $store->name }}</h5>
                                        <p class="card-text">{{ $store->address }}</p>
                                        <a href="#" class="btn btn-primary">{{ $store->phone }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('modals.add-store-modal')
@endsection
@section('scripts')
    <script>
        $("#addStore").click(function() {
            $("#addStoreModal").modal('show');
        });
        $("#saveStore").click(function() {
            const formData = new FormData();
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
                    const error = response.responseJSON.error_message;
                    $("#store_name_error").text(error.store_name ? error.store_name[0] : '');
                    $("#store_address_error").text(error.store_address ? error.store_address[0] : '');
                    $("#store_phone_error").text(error.store_phone ? error.store_phone[0] : '');
                    $("#store_email_error").text(error.store_email ? error.store_email[0] : '');
                    $("#store_logo_error").text(error.store_logo ? error.store_logo[0] : '');
                    console.log(error)
                }

            });
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
    </script>
@endsection
