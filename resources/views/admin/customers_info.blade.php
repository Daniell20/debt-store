<!-- Modal Header -->
<div class="modal-header">
	<h4 class="modal-title">{{ $customer->name }}'s Personal Info</h4>
</div>

<!-- Modal body -->
<form class="form-group" id="customerInfoForm">
	<div class="modal-body">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" id="customer_id" name="customer_id" value="{{ $customer->customer_id }}">

			<div class="row">
				<div class="col-md-6">
					<label for="email" class="form-label">Email</label>
					<input type="text" class="form-control" value="{{ $customer->email }}" disabled>
				</div>

				<div class="col-md-6">
					<label for="secret" class="form-label">Password</label>
					<input type="text" class="form-control" value="{{ $customer->secret }}" disabled>
				</div>
			</div>

			<div class="row mt-3">
				<div class="col-md-6">
					<label for="name" class="form-label">Name</label>
					<input type="text" class="form-control" name="name" value="{{ $customer->name }}">
				</div>

				<div class="col-md-6">
					<label for="contact-number" class="form-label">Contact Number</label>
					<input type="text" class="form-control" name="contact_number" value="{{ $customer->contact_number }}">
				</div>
			</div>

			<div class="row mt-3">
				<div class="col-md-12">
					<label for="address" class="form-label">Address</label>
					<input type="text" class="form-control" name="address" value="{{ $customer->address }}">
				</div>
			</div>
	</div>

	<!-- Modal footer -->
	<div class="modal-footer">
		<button type="submit" id="updateButton" class="btn btn-success"><span class="ti ti-pencil"></span> Update</button>
		<button type="button" id="deactivateButton" class="btn btn-warning"><span class="ti ti-exclamation-circle"></span> {{ $customer->is_active == 0 ? "Activate" : "Deaactivate"  }}</button>
		<button type="button" class="btn btn-danger" data-bs-dismiss="modal"><span class="ti ti-x"></span> Close</button>
	</div>
</form>


<script>
	
</script>