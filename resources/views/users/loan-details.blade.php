<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="form-floating">
                <select name="months_to_pay" id="months_to_pay" class="form-select">
                    <option value="0" hidden selected>--</option>
                    @foreach ($loan_settings as $loan_setting)
                        <option value="{{ $loan_setting->months_to_pay }}" data-loan_settings_id="{{ $loan_setting->id }}" data-interest_rate="{{ $loan_setting->interest_rate }}">{{ $loan_setting->months_to_pay }} Months</option>
                    @endforeach
                </select>
                <label for="months-to-pay" class="form-label">Months To Pay</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <select name="interest_rate" id="interest_rate" class="form-select" disabled>
                    <option value="0" hidden selected>--</option>
                </select>
                <label for="interest" class="form-label">Interest Rate</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 d-flex align-item-stretch">
            <div class="card w-100">
                <div class="card-body d-flex justify-content-center">
                    <img style="max-width: 100%; height: 200px;" class="img-fluid rounded" src="{{ asset($products['image']) }}" alt="">
                </div>
            </div>
        </div>  
        <div class="col-lg-4 d-flex align-item-stretch">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title col-lg-12">Price <span class="float-end"><span class="ti ti-currency-peso"></span> {{ $products["price"] }}</span></h5>
                    
                </div>
                <div class="card-body">
                    <h6> Powered by: <img style="width: 5rem;" src="{{ asset("images/logos/paymongo.png") }}" alt=""> </h6>
                </div>
                <div class="card-footer d-flex justify-content-center">
                    <button class="btn btn-success" style="margin-right: 10px;" id="loanProductButton" disabled>Proceed </button>
                    <button class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).on("change", "#months_to_pay", function () {
        var selectedOption = $(this).find("option:selected");
        var interestRate= selectedOption.data("interest_rate");

        if (interestRate) {
            $("#loanProductButton").prop("disabled", false);
            $("#interest_rate").empty();
            $("#interest_rate").append(`<option value="${interestRate}">${interestRate} %</option>`);
        } else {
            $("#loanProductButton").prop("disabled", true);
        }

    });

    $(document).on("click", "#loanProductButton", function () {
        $(this).prop("disabled", true);

        var postStoreId = "{!! $products->store_id !!}"; 
        var postProductId = "{!! $products->id !!}";
        var monthsToPay = $("#months_to_pay").val();
        var loanSettingsId = $("#months_to_pay").find("option:selected").data("loan_settings_id");
        var interestRate = $("#interest_rate").val();

        console.log(loanSettingsId)
        $.ajax({
            url: "{{ route('users.loan.product') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                product_id: postProductId,
                loan_settings_id: loanSettingsId,
                months_to_pay: monthsToPay,
                interest_rate: interestRate,
            },
            success: function (response) {
                if (response) {
                    iziToast.success({
                        title: "Success",
                        message: "You successfully loan the item!",
                        position: "topRight",
                        transitionIn: "bounceInDown",
                        transitionOut: "flipOutX",
                        timeout: 2000,
                        onClosing: function () {
                            location.href = "{{ route('users.dashboard') }}";
                        },
                    });
                }
            },
        });
    });
</script>
