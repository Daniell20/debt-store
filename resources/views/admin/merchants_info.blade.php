<div class="modal-header">
	<h1 class="modal-title fs-5" id="exampleModalLabel">{{ $merchant->name }}'s Personal Info</h1>
</div>
<form id="updateMerchantForm">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="merchant_id" value="{{ $merchant->merchant_id }}">
	<div class="modal-body">
		<div class="form-group">
			<div class="row">
				<div class="col-6">
					<label for="user-name" class="form-label">Username</label>
					<input type="text" class="form-control" value="{{ $merchant->email }}" disabled>
				</div>
				
				<div class="col-6">
					<label for="secret" class="form-label">Password</label>
					<input type="text" class="form-control" value="{{ $merchant->secret }}" disabled>
				</div>
			</div>

			<div class="row mt-3">
				<div class="col-6">
					<label for="merchant-name" class="form-label">Merchant Name</label>
					<input type="text" name="merchant_name" class="form-control" id="merchant_name" value="{{ $merchant->name }}">
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="submit" id="merchantSubmitButton" class="btn btn-success"><span class="ti ti-pencil"></span> Update</button>
		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><span class="ti ti-x"></span> Close</button>
	</div>
</form>