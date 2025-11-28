<!-- Edit Property Modal -->
<div id="edit-property-container" class="modal-overlay" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Edit Property</span>
            <button id="edit-close" class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <form id="edit-property-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="edit-property-id" name="property_id">

            <div>
                <label>Category</label>
                <input type="text" id="edit-category" name="category" required>
            </div>
            <div>
                <label>Location</label>
                <input type="text" id="edit-location" name="location" required>
            </div>
            <div>
                <label>Price</label>
                <input type="number" id="edit-price" name="price" required>
            </div>
            <div>
                <label>Status</label>
                <select id="edit-status" name="status" required>
                    <option value="available">Available</option>
                    <option value="sold">Sold</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div>
                <label>Description</label>
                <textarea id="edit-description" name="description" rows="3"></textarea>
            </div>
            <div>
                <label>Transaction Type</label>
                <select id="edit-transaction-type" name="transaction_type" required>
                <option value="sale">Sale</option>
                <option value="rent">Rent</option>
                </select>
            </div>

            <div>
                <label>Installment Years</label>
                <input type="number" id="edit-installment-years" name="installment_years" min="0">
            </div>
            <div>
                <label>Property Image</label>
                <input type="file" id="edit-image" name="image" accept="image/*">
            </div>
            <div class="multi-upload-group">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="mb-0">Additional Images</label>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="edit-add-multi-image-btn">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="multi-images-wrapper" id="edit-multi-images-wrapper">
                    <input type="file" class="form-control mb-2" name="multiple_images[]" accept="image/*">
                </div>
            </div>
            @auth
            @if(auth()->user()->role === 'admin')
            <div>
                <label>User ID</label>
                <input type="number" id="edit-user-id" name="user_id" required>
            </div>
            @endif
            @endauth
            <div class="modal-actions">
                <button type="button" id="edit-cancel" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
