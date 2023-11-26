<div class="modal-content">
	<!-- Modal Header -->
	<div class="modal-header">
		<h4 class="modal-title">Loan Interest Status</h4>
	</div>

	<!-- Modal body -->
	<div class="modal-body">
		<div class="table-responsive">
			<table class="table table-striped" id="loanStatusTable">
				<thead>
					<tr>
						<th>Interest Rate</th>
						<th>Calculation Date</th>
						<th>Calculated Amount</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

	<!-- Modal footer -->
	<div class="modal-footer">
		<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
	</div>
</div>

<script>
	var loanStatusTable;
	var debtId = "{!! $debt_id !!}";
	$(document).ready(function () {
		loanStatusTable = $("#loanStatusTable").DataTable({
			ajax: {
				url: "{{ route('users.interest_data') }}",
				type: "GET",
				data: function (data) {
					data.debt_id = debtId
				},
				dataSrc: "",
			},
			columns: [
				{data: "interest_rate"},
				{data: "calculation_date"},
				{data: "calculation_amount"},
				{data: "status"},
			],
		});
	});
</script>
