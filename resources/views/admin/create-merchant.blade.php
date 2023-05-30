@extends('layouts.main_layout')\
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
                        <button class="float-end btn btn-primary btn-sm" id="addMerchantButton"><span
                                class="ti ti-plus"></span> Add</button>
                    </div>
                    <table class="table" id="merchantTable">
                        <thead>
                            <tr>
                                <th>Merchant Name</th>
                                <th>Merchant Username</th>
                                <th>Merchant Default Password</th>
                                <th>Activate Merchant</th>
                            </tr>
                        </thead>
                        <tbody id="merchantData">
                            @foreach ($merchants as $merchant)
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
                            @endforeach
                        </tbody>
                    </table>
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
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
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
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
</script>
@endsection
