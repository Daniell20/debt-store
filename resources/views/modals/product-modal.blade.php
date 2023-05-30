<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="editDeleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDeleteProductModalLabel">Edit/Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="edit_product_id">
                    <div class="form-group">
                        <label for="editproduct_image">Product Image</label>
                        <input type="file" name="product_image" id="edit_product_image" class="form-control"> 
                        Current Image: <span id="current_image_name" class="d-none"></span>
                    
                        <p id="product_image_error" class="text-danger"></p>
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
