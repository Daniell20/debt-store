<div class="modal fade" id="addStoreModal" tabindex="-1" aria-labelledby="addStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addStoreModalLabel">Add Store</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data">
                    <div class="form-group px-2 mb-4">
                        <label for="store_logo">Store Logo</label>
                        <input type="file" class="form-control" id="store_logo" name="store_logo">

                        <p class="text-danger" id="store_logo_error"></p>
                    </div>
                    <div class="form-group px-2 mb-4">
                        <label for="store_name">Store Name</label>
                        <input type="text" class="form-control" id="store_name" name="store_name">

                        <p class="text-danger" id="store_name_error"></p>
                    </div>
                    <div class="form-group px-2 mb-4">
                        <label for="address">Store Address</label>
                        <input type="text" class="form-control" id="store_address" name="store_address">

                        <p class="text-danger" id="store_address_error"></p>
                    </div>
                    <div class="form-group px-2 mb-4">
                        <label for="phone">Store Phone</label>
                        <input type="text" class="form-control" id="store_phone" name="store_phone">

                        <p class="text-danger" id="store_phone_error"></p>
                    </div>
                    <div class="form-group px-2 mb-4">
                        <label for="email">Store Email</label>
                        <input type="email" class="form-control" id="store_email" name="store_email">

                        <p class="text-danger" id="store_email_error"></p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveStore">Save changes</button>
            </div>
        </div>
    </div>
</div>
