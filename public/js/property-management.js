$(document).ready(function() {

    // ---------------- Toast Notifications ----------------
    function showToast(message, type = 'error') {
        const toastContainer = $('#toast-container');
        if (!toastContainer.length) return;

        let iconClass = 'fa-exclamation-circle';
        if (type === 'success') iconClass = 'fa-check-circle';
        else if (type === 'warning') iconClass = 'fa-exclamation-triangle';
        else if (type === 'info') iconClass = 'fa-info-circle';

        const toast = $(`<div class="toast ${type}">
            <i class="fas ${iconClass} toast-icon"></i>
            <span class="toast-message">${message}</span>
            <button class="toast-close"><i class="fas fa-times"></i></button>
        </div>`);

        toastContainer.append(toast);
        toast.find('.toast-close').click(() => toast.remove());
        setTimeout(() => toast.fadeOut(300, () => toast.remove()), 5000);
    }

    window.pmShowToast = showToast;

    const createMultiInput = () => $('<input type="file" class="form-control mb-2" name="multiple_images[]" accept="image/*">');

    $('#add-multi-image-btn').on('click', function(e) {
        e.preventDefault();
        const wrapper = $('#multi-images-wrapper');
        if (wrapper.length) {
            wrapper.append(createMultiInput());
        }
    });

    $('#edit-add-multi-image-btn').on('click', function(e) {
        e.preventDefault();
        const wrapper = $('#edit-multi-images-wrapper');
        if (wrapper.length) {
            wrapper.append(createMultiInput());
        }
    });

    // ---------------- Client-side Search ----------------
    $('.search-bar input').on('keyup', function() {
        let query = $(this).val().toLowerCase();
        $('#properties-list tbody tr').each(function() {
            let rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(query) > -1);
        });
    });

    // ---------------- Add Property ----------------
    $('#add-property-form').submit(function(e){
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                showToast('Property added successfully!', 'success');

                const property = res.property;
                const newRow = `<tr data-description="${property.description || ''}" data-installment="${property.installment_years || 0}" data-transaction="${property.transaction_type || ''}">
                    <td>${property.id}</td>
                    <td>${property.category}</td>
                    <td>${property.location}</td>
                    <td>${Number(property.price).toLocaleString()} EGP</td>
                    <td>${property.status.charAt(0).toUpperCase() + property.status.slice(1)}</td>
                    <td>${property.user_id}</td>
                    <td>
                        <button class="btn btn-secondary btn-sm edit-btn" data-id="${property.id}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${property.id}"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`;
                $('#properties-list tbody').prepend(newRow);
                $('#add-property-form')[0].reset();
            },
            error: function(xhr){
                showToast('Error adding property: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
            }
        });
    });

    // ---------------- File Input Display ----------------
    $('#property-image').on('change', function() {
        console.log(this.files); // optional debug
    });
});
