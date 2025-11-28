$(document).ready(function() {

    let deleteUrl = null;
    let rowToRemove = null;

    function openModal(url = null, row = null) {
        deleteUrl = url;
        rowToRemove = row;
        $('#modal-overlay').show();
    }

    function closeModal() {
        deleteUrl = null;
        rowToRemove = null;
        $('#modal-overlay').hide();
    }

    $('#modal-close, #modal-cancel').click(closeModal);

    $('#delete-form').submit(function(e) {
        e.preventDefault();
        if(deleteUrl) {
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: $(this).serialize() + '&_method=DELETE',
                success: function() {
                    showToast('Property deleted successfully!', 'success');
                    if(rowToRemove) rowToRemove.remove();
                    closeModal();
                },
                error: function(xhr) {
                    showToast('Error deleting property: ' + xhr.responseText, 'error');
                }
            });
        }
    });

    $(document).on("click", ".delete-btn", function() {
        const button = $(this);
        const propertyId = button.data("id"); // خذ ID من data-id
        if(!confirm("Are you sure you want to delete this property?")) return;
    
        $.ajax({
            url: `/properties/${propertyId}`,
            type: "POST",
            data: {
                _method: "DELETE",
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if(res.success) {
                    // حذف الصف من الجدول مباشرة
                    button.closest("tr").remove();
                    showToast("Property deleted successfully!", "success");
                }
            },
            error: function(xhr){
                showToast("Error deleting property: " + xhr.statusText, "error");
            }
        });
    });
    

});
