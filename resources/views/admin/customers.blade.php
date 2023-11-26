@extends('layouts.main_layout')
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
							Customers
						</div>
						<div class="table-responsive">
							<table class="table table-striped" id="customerTable">
								<thead>
									<tr>
										<th>Customer Name</th>
										<th>Customer Username</th>
										<th>Customer Default Password</th>
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
	</div>

	{{-- Modal --}}
	<div class="modal fade" id="customerInfoModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content" id="modalData">		
			</div>
		</div>
	</div>
@endsection
@section("scripts")
	<script>
		var customerTable;
		$(document).ready(function () {
			customerTable = $("#customerTable").DataTable({
				ajax: {
					url: "{{ route('admin.customers.data') }}",
					type: "GET",
					data: function (data) {

					},
					dataSrc: "",
				},
				columns: [
					{data: "customer_name"},
					{data: "customer_username"},
					{data: "customer_password"},
					{data: "status"},
					{data: "action"},
				],
			});

			$("#customerInfoModal").modal({
				backdrop: "static",
				keyboard: false,
			});
		});

		$(document).on("click", ".customerData", function () {
			var customerId = $(this).data("customer_id");

			$.ajax({
				url: "{{ route('admin.customer.info') }}",
				type: "GET",
				data: {
					customer_id: customerId,
				},
				success: function (response) {
					if (response) {
						$("#customerInfoModal").modal("show");
						$("#modalData").html(response);
					}
				}
			});
		});

		$(document).on("submit", "#customerInfoForm", function (e) {
			e.preventDefault(); // Prevent the default form submission
			$("#customerInfoModal").modal("hide");
			
			iziToast.question({
				timeout: 20000,
				close: false,
				overlay: true,
				displayMode: "once",
				id: "question",
				zindex: 999,
				title: "Hey",
				message: "Are you sure about that?",
				position: "center",
				buttons: [
					[
						'<button><b>Yes</b></button>', function (instance, toast) {
							instance.hide({
								transitionOut: "fadeOut"
							}, toast, "button");

								var customerInfoForm = new FormData(document.getElementById("customerInfoForm"));

								$.ajax({
									url: "{{ route('admin.customers.update') }}",
									type: "POST",
									data: customerInfoForm,
									processData: false, // Prevent jQuery from processing data
									contentType: false, // Prevent jQuery from setting content type
									success: function (response) {
										if (response.error) {
											$("#customerInfoModal").modal("show");

											$.each(response.error, function (index, value) {
												iziToast.error({
													title: "Oops",
													message: value,
													position: "topRight",
													transitionOut: "flipOutX",
													transitionIn: "bounceInDown",
												});
											});
										} else if (response.success) {
											iziToast.success({
												title: "Success",
												message: "Customer updated.",
												position: "topRight",
												transitionIn: "bounceInDown",
												transitionOut: "flipOutX",
											});

											customerTable.ajax.reload();
										}
									},
								});

						}, true
					],
					[
						'<button>NO</button>', function (instance, toast) {
							instance.hide({
								transitionOut: "fadeOut",
							}, toast, "button");
							
							$("#customerInfoModal").modal("show");
						}
					],
				],
			});
		});

		$(document).on("click", "#deactivateButton", function (e) {
			e.preventDefault();
			$("#customerInfoModal").modal("hide");
			var customerId = $("#customer_id").val();

			iziToast.question({
				timeout: 20000,
				close: false,
				overlay: true,
				displayMode: "once",
				id: "deactivateQuestion",
				zindex: 999,
				title: "Confirmation",
				message: "Are you sure you about that?",
				position: "center",
				buttons: [
					[
						'<button><b>Yes</b></button>', function (instance, toast) {
							// Perform the deactivation action here
							instance.hide({
								transitionOut: "fadeOut"
							}, toast, "button");

							// You can make an AJAX request to deactivate the customer here
							$.ajax({
								url: "{{ route('admin.customers.deactivate') }}",
								type: "POST",
								data: { 
									customer_id: customerId, 
									_token: "{{ csrf_token() }}" 
								},
								success: function (response) {
									if (response) {
										iziToast.success({
											title: "Successfully",
											message: "Deactivated.",
											position: "topRight",
											transitionIn: "bounceInDown",
											transitionOut: "flipOutX",
										});

										customerTable.ajax.reload();
									}
								},
							});
						}, true
					],
					[
						'<button>NO</button>', function (instance, toast) {
							instance.hide({
								transitionOut: "fadeOut",
							}, toast, "button")
						}
					],
				],
			});
		});
	</script>
@endsection