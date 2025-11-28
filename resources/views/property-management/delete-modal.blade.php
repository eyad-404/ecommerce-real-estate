<!-- Delete Property Modal -->
<div id="modal-overlay" class="modal-overlay" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Confirm Deletion</span>
            <button id="modal-close" class="modal-close"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">Are you sure you want to delete this property?</div>
        <div class="modal-actions">
            <button id="modal-cancel" class="btn btn-secondary">Cancel</button>
            <form id="delete-form" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>
