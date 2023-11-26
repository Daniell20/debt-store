<div class="modal fade" id="addProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addProductModalLabel">Add Product</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" enctype="multipart/form-data">
                    <div class="form-group px-2 mb-4">
                        <label for="product_image">Product Image</label>
                        <input type="file" name="product_image" id="product_image" class="form-control product_image">
                        <div class="d-flex justify-content-center">
                            <img class="img-fluid productImagePreview" style="max-width: 100%; max-height: 200px; display: none;">
                        </div>
                        <p class="text-danger" id="product_logo_error"></p>
                    </div>

                    <div class="form-group mb-4 px-2">
                        <label for="store">Select Store</label>
                        <select name="store" id="store_id" class="form-control">
                            <option value="" hidden></option>
                            @foreach ($stores as $store)
                                <option value="{{ $store['store_id'] }}">{{ $store['store_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group px-2 mb-4">
                        <label for="item">Product Name</label>
                        <input type="text" name="product_name" id="product_name" class="form-control">
    
                        <p id="product_name_error" class="text-danger"></p>
                    </div>
                    <div class="form-group px-2 mb-4">
                        <label for="item">Product Price</label>
                        <input type="text" name="product_price" id="product_price" class="form-control">
    
                        <p id="product_price_error" class="text-danger"></p>
                    </div>
                    <div class="form-group px-2 mb-4 pe-auto">
                        <label for="item">Product Description</label>
                        <textarea type="text" name="product_description" id="product_description" class="form-control"></textarea>
    
                        <p id="product_description_error" class="text-danger"></p>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submitProductModalButton">Submit <span id="saveStoreLoader"></span></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>