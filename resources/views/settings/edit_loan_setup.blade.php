<!-- Modal Header -->
<div class="modal-header">
	<h4 class="modal-title">Update Loan Setup For Your Store</h4>
</div>

<!-- Modal body -->
<div class="modal-body">
	<form class="form-group" id="loanSetupUpdateForm">

		<input type="hidden" name="loan_settings_id" id="loan_settings_id" value="{{ $loan_settings->id }}">

		<div class="row">
			<div class="col-md-6">
				<div class="form-floating mb-3">
					<input type="number" class="form-control" id="interest_rate" name="interest_rate" value="{{ $loan_settings->interest_rate }}" placeholder="5%">
					<label for="interest-rate">Interest Rate (%)</label>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-floating mb-3">
					<input type="number" class="form-control" id="months_to_pay" name="months_to_pay" value="{{ $loan_settings->months_to_pay }}" placeholder="5 mos">
					<label for="months-to-pay">Months To Pay</label>
				</div>
			</div>
		</div>
	</form>
</div>


<!-- Modal footer -->
<div class="modal-footer">
	<button type="button" id="updateLoanSetupButton" class="btn btn-success loanSetupButton"><span class="ti ti-plus"></span> Save</button>
	<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
</div>