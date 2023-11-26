<div class="modal fade" id="storeModal" tabindex="-1" aria-labelledby="editDeleteStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDeleteStoreModalLabel">Edit/Delete Store</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" enctype="multipart/form-data" id="editStoreForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_store_id">
                    <div class="form-group px-2 mb-4">
                        <label for="store_logo">Store Logo</label>
                        <input type="file" class="form-control store_logo" id="store_logo" name="store_logo">
                        <div class="d-flex justify-content-center">
                            <img class="img-fluid imagePreview" src="" alt="Selected Image" style="max-width: 100%; max-height: 200px; display: none">
                        </div>
                        <p class="text-danger" id="store_logo_error"></p>
                    </div>

                    <div class="form-group px-2 mb-4">
                        <label for="edit_store_name">Store Name</label>
                        <input type="text" class="form-control" id="edit_store_name">

                        <p id="store_name_error" class="text-danger"></p>
                    </div>
                    <div class="form-group px-2 mb-4">
                        <label for="edit_store_address">Store Address</label>
                        <input type="text" class="form-control" id="edit_store_address">

                        <p id="store_address_error" class="text-danger"></p>
                    </div>
                    <div class="form-group px-2 mb-4">
                        <label for="edit_store_phone">Store Phone</label>
                        <textarea class="form-control" id="edit_store_phone"></textarea>

                        <p id="store_phone_error" class="text-danger"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteStoreButton">Delete</button>
                    <button type="button" class="btn btn-primary" id="updateStore">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
