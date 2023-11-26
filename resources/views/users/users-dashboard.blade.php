@extends('layouts.main_layout')
@section("styles")
    <style>
        
    </style>
@endsection
@section('content')
    @include('layouts.users.sidebar')
    <div class="body-wrapper">
        @include('layouts.navbar')
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <?php
                            // total_customer_interests
                            $total_customer_interests = $customer_interests->sum("calculated_interest_amount");

                            // getting credit left
                            $credit_avail_date = $user_customer->updated_at;
                            $user_credit_limit = $user_customer->credit_limit;
                            $user_debts = $user_debts->sum("amount");
                            $total_payments = $transactions->sum("amount");
                            $credit_left = $user_credit_limit - ($user_debts - $total_payments);

                            $current_date = date('Y-m-d'); // Current date
                            $credit_avail_date_obj = new DateTime($credit_avail_date);
                            $current_date_obj = new DateTime($current_date);
                            $interval = $current_date_obj->diff($credit_avail_date_obj);
                            $months_difference = $interval->format('%m') + 12 * $interval->format('%y');

                            // Calculate the credit used during that time frame
                            $credit_used = $user_credit_limit - $credit_left;

                            // Calculate the percentage of credit usage
                            $percentage_used = ($credit_used / ($user_credit_limit + $user_debts)) * 100;
                        ?>
                        <div class="col-lg-6">
                            <!-- Credit Limit -->
                            <div class="card overflow-hidden">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-9 fw-semibold">Available Credit</h5>
                                    <div class="row align-items-center">
                                        <div class="col-8">
                                            <h4 class="fw-semibold"><span class="ti ti-currency-peso"></span> {{ $credit_left }}</h4>
                                        </div>
                                        <div class="col-4">
                                            <div id="userCredit"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <!-- Debt Amount -->
                            <div class="card overflow-hidden">
                                <div class="card-body p-4">
                                    <div class="row align-items-start">
                                        <div class="col-8">
                                            <?php $updated_debt = $user_debts - $total_payments; ?>
                                            <h5 class="card-title mb-9 fw-semibold"> Debt Balance </h5>
                                            <h4 class="fw-semibold mb-3"><span class="ti ti-currency-peso"></span>  {{ $updated_debt + $total_customer_interests }}</h4>
                                        </div>
                                        <div class="col-4">
                                            <div class="d-flex justify-content-end">
                                                @if ($updated_debt > 0)
                                                    <div id="payDebtsButton" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Pay" class="text-white bg-success rounded-circle p-6 d-flex align-items-center justify-content-center">
                                                        <i class="ti ti-cash fs-6"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="debtGraph"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 d-flex align-item-strech">
                    <div class="card w-100">
                        <div class="card-body p-4">
                            {{-- Debt Product --}}
                            <h5 class="card-title fw-semibold mb-4">Recent Loan Transaction</h5>
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0 align-middle table-striped" id="recentLoanTransactionTable">
                                    <thead class="text-dark fs-4">
                                        <tr>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-bold mb-0">Product</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-bold mb-0">Name</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-bold mb-0">Date Loaned</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-bold mb-0">Due Date</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-bold mb-0">Amount</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-bold mb-0">Action</h6>
                                            </th>
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
            <div class="row">
                <div class="col-lg-12 d-flex align-item-strech">
                    <div class="card w-100">
                        <div class="card-body p-4">
                            {{-- Debt Product --}}
                            <h5 class="card-title fw-semibold mb-4">Recent Payment Transaction</h5>
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0 align-middle table-striped" id="recentPaymentTransaction">
                                    <thead class="text-dark fs-4">
                                        <tr>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Transaciton ID</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Amount</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Status</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Payment Method</h6>
                                            </th>
                                            <th class="border-bottom-0">
                                                <h6 class="fw-semibold mb-0">Transaction Date</h6>
                                            </th>
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
        </div>
    </div>

    {{-- Payment Form Modal --}}
    <div class="modal fade modal-lg" id="paymetModal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
        
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Pay Your Debt</h4>
                </div>
        
                <!-- Modal body -->
                <div class="modal-body">
                    <form class="form-group" id="paymentForm" method="POST" action="{{ route('users.loan.payment') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-floating mb-3 mt-3">
                            <input type="number" class="form-control" id="amount" placeholder="" name="amount" required>
                            <label for="">Amount</label>
                        </div>
                        <div class="float-start">
                            <button type="submit" id="payGcashButton" class="btn btn-primary">Pay with GCash</button>
                        </div>
                    </form>
                </div>
        
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
        
            </div>
        </div>
    </div>

    {{-- View Debt Status Modal --}}
    <div class="modal fade" id="viewDebtStatusModal">
        <div class="modal-dialog modal-xl" id="viewDebtStatusContent">
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        var status_session = "{!! session('status') !!}"; 
        var recentLoanTransactionTable;
        var recentPaymentTransaction;

        const userCreditBalance = {!! json_encode($user_credit_limit) !!};
        const userDebts = {!! json_encode($updated_debt) !!};
        const userCreditLeft = {!! json_encode($credit_left) !!};
        const userLoanAmount = {!! json_encode($loan_amount_data) !!};
        const totalCustomerInterest = parseInt({!! json_encode($total_customer_interests) !!});

        $(document).ready(function () {

            recentLoanTransactionTable = $("#recentLoanTransactionTable").DataTable({
                ajax: {
                    url: "{{ route('users.recent_transaction_data') }}",
                    type: "GET",
                    data: function (data) {

                    },
                    dataSrc: "",
                },
                columns: [
                    {data: "product"},
                    {data: "name"},
                    {data: "date_loaned"},
                    {data: "due_date"},
                    {data: "amount"},
                    {data: "action"},
                ],
            });

            recentPaymentTransaction = $("#recentPaymentTransaction").DataTable({
                ajax: {
                    url: "{{ route('users.recent_payment_transaction_data') }}",
                    type: "GET",
                    data: function (data) {

                    },
                    dataSrc: "",
                },
                columns: [
                    {data: "transaction_id"},
                    {data: "amount"},
                    {data: "status"},
                    {data: "payment_method"},
                    {data: "transaction_date"},
                ],
            });

            $("#paymentForm").on("submit", function () {
                $('#payGcashButton').prop('disabled', true);
            });

            if (status_session == "success") {
                iziToast.success({
                    title: "Success",
                    message: "Your payment has been processed.",
                    position: "center",
                    transitionIn: "bounceInDown",
                    transitionOut: "flipOutX",
                });
            } else if (status_session == "error") {
                iziToast.error({
                    title: "Oops",
                    message: "{{ session('error_message') }}",
                    position: "center",
                    transitionIn: "bounceInDown",
                    transitionOut: "flipOutX",
                });
            }

            $("#payDebtsButton").click(function () {
                $("#paymetModal").modal("show");
            });

            $("#paymetModal").modal({
                backdrop: "static",
                keyboard: false,
            });

            $("#amount").on("input", function () {
                var amountInput = $(this).val();
                if (amountInput > (userDebts + totalCustomerInterest)) {
                    $("#payGcashButton").prop("disabled", true);
                    iziToast.error({
                        title: "Oops",
                        message: "Amount is greater than debt balance.",
                        position: "topRight",
                        transitionIn: "bounceInDown",
                        transitionOut: "flipOutX",
                    });
                } else (
                    $("#payGcashButton").prop("disabled", false)
                )
            });

            $("#viewDebtStatusModal").modal({
                backdrop: "static",
                keyboard: false,
            });
        });
    </script>
    <script src="{{ asset("js/user_dashboard.js") }}"></script>
    <script>
        $(document).on("click", ".viewDebtsStatusButton", function () {
            const debtsId = $(this).data("debts_id");

            $.ajax({
                url: "{{ route('users.interest') }}",
                type: "GET",
                data: {
                    debt_id: debtsId,
                },
                success: function (response) {
                    if (response) {
                        $("#viewDebtStatusModal").modal("show");
                        $("#viewDebtStatusContent").html(response);
                    }
                }
            });
        });
    </script>
@endsection