<div class="row mb-3">

    <input type="hidden" name="customer_id" value="{{ $customer->customer_id }}">

    <div class="col-md-4">
        <label for="username" class="form-label">Username</label>
        <input class="form-control" type="text" value="{{ $customer->email }}" disabled>
    </div>
    
    <div class="col-md-4">
        <label for="password" class="form-label">Password</label>
        <input class="form-control" type="text" value="{{ $customer->secret }}" disabled>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label for="name" class="form-label">Name</label>
        <input class="form-control" type="text" name="name" value="{{ $customer->name }}">
    </div>

    <div class="col-md-4">
        <label for="address" class="form-label">Address</label>
        <input class="form-control" type="text" name="address" value="{{ $customer->address }}">
    </div>

    <div class="col-md-4">
        <label for="contact-number" class="form-label">Contact Number</label>
        <input class="form-control" type="text" name="contact_number" value="{{ $customer->contact_number }}">
    </div>

</div>

<div class="row">
    <div class="col-md-4">
        <label for="email" class="form-label">Email</label>
        <input class="form-control" type="text" name="email" value="{{ $customer->email }}">
    </div>

    <div class="col-md-4">
        <label for="credit-limit" class="form-label">Credit Limit</label>
        <input class="form-control" type="text" name="credit_limit" value="{{ $customer->credit_limit }}">
    </div>

    <div class="col-md-4">
        <label for="status" class="form-label">Status</label>
        <input class="form-control" type="text" value="{{ $customer->is_active == 1 ? "Active" : "Deactivate" }}" disabled>
    </div>
</div>
