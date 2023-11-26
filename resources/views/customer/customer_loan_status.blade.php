@extends('layouts.main_layout')
@section('content')
    @include('layouts.sidebar')

    <div class="body-wrapper">

        @include('layouts.navbar')

        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
						Customers Loan
					</div>
				</div>
				<div class="card-body">
					<div class="row table-responsive">
						<table class="table table-striped table-bordered table-hover" id="customerLoanListTable">
							<thead>
								<tr>
									<th>Customer Name</th>
									<th>Product Owed</th>
									<th>Amount</th>
									<th>Interest Rate</th>
									<th>Status</th>
									<th>Action</th>
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

	{{-- Modal --}}
	<div class="modal fade modal-lg" id="interestModal">
		<div class="modal-dialog modal-xl" id="interestDetailData">
			
		</div>
	</div>
@endsection
@section("scripts")
	<script>
		var customerLoanListTable;
		$(document).ready(function () {
			customerLoanListTable = $("#customerLoanListTable").DataTable({
				ajax: {
					url: "{{ route('merchant.customer_loan_status_data') }}",
					type: "GET",
					data: function () {

					},
					dataSrc: "",
				},
				columns: [
					{data: "customer_name"},
					{data: "product_owed"},
					{data: "amount"},
					{data: "interest_rate"},
					{data: "debt_status"},
					{data: "action"},
				],
			});

			$("#interestModal").modal({
				backdrop: "static",
				keyboard: false,
			});
			
		});

		$(document).on("click", ".viewInterestButton", function () {
			var debtId = $(this).data("debts_id");

			$.ajax({
				url: "{{ route('merchant.customer_loan_interest_status') }}",
				type: "GET",
				data: {
					debt_id: debtId,
				},
				success: function (response) {
					if (response) {
						$("#interestModal").modal("show");
						$("#interestDetailData").html(response);
					}
				}
			});
		});
	</script>
@endsection