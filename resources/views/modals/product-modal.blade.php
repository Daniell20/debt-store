<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="editDeleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDeleteProductModalLabel">Edit/Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" enctype="multipart/form-data" id="editProductForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_product_id">
                    <div class="form-group px-2 mb-4">
                        <label for="product_image">Product Image</label>
                        <input type="file" name="product_image" id="product_image" class="form-control product_image">
                        <div class="d-flex justify-content-center">
                            <img class="img-fluid productImagePreview" style="max-width: 100%; max-height: 200px; display: none;">
                        </div>
                        <p class="text-danger" id="product_logo_error"></p>
                    </div>
                    <div class="form-group">
                        <label for="edit_product_name">Product Name</label>
                        <input type="text" class="form-control" id="edit_product_name">

                        <p id="product_name_error" class="text-danger"></p>
                    </div>
                    <div class="form-group">
                        <label for="edit_product_price">Product Price</label>
                        <input type="text" class="form-control" id="edit_product_price">

                        <p id="product_price_error" class="text-danger"></p>
                    </div>
                    <div class="form-group">
                        <label for="edit_product_description">Product Description</label>
                        <textarea class="form-control" id="edit_product_description"></textarea>

                        <p id="product_description_error" class="text-danger"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteProductButton">Delete</button>
                    <button type="button" class="btn btn-primary" id="updateProduct">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
