$(document).ready(function () {
    // ------------------- Open Edit Modal -------------------
    $(document).on("click", ".edit-btn", function () {
        const row = $(this).closest("tr");

        const property = {
            id: row.find("td:first-child").text().trim(),
            category: row.find("td:nth-child(2)").text().trim(),
            location: row.find("td:nth-child(3)").text().trim(),
            price: row
                .find("td:nth-child(4)")
                .text()
                .replace(" EGP", "")
                .replace(/,/g, "")
                .trim(),
            status: row.find("td:nth-child(5)").text().trim().toLowerCase(),
            user_id: row.find("td:nth-child(6)").text().trim() || undefined,
            description: row.data("description") || "",
            installment_years: row.data("installment") || undefined,
            transaction_type: row.data("transaction") || "sale",
        };

        // Fill modal inputs
        $("#edit-property-id").val(property.id);
        $("#edit-category").val(property.category);
        $("#edit-location").val(property.location);
        $("#edit-price").val(property.price);
        $("#edit-status").val(property.status);
        $("#edit-user-id").val(property.user_id);
        $("#edit-description").val(property.description);
        $("#edit-installment-years").val(property.installment_years);
        $("#edit-transaction-type").val(property.transaction_type);

        $("#edit-property-container").fadeIn(200);
    });

    // ------------------- Close Edit Modal -------------------
    function closeModal() {
        $("#edit-property-container").fadeOut(200);
        const form = $("#edit-property-form")[0];
        if (form) form.reset();
    
        // Reset multi-image inputs to just one
        const wrapper = $("#edit-multi-images-wrapper");
        if (wrapper.length) {
            wrapper.find('input[type="file"]').slice(1).remove();
        }
    }
    

    $("#edit-close, #edit-cancel").click(closeModal);

    // ------------------- Toast Function -------------------
    function showToast(message, type = "error") {
        if (window.pmShowToast) return window.pmShowToast(message, type);
        alert(message);
    }

    // ------------------- Submit Edit Form -------------------
    $("#edit-property-form").submit(function (e) {
        e.preventDefault();

        const propertyId = $("#edit-property-id").val();
        if (!propertyId) {
            showToast("Property ID is missing!", "error");
            return false;
        }

        // Build payload
        const payload = {
            _method: "PUT",
            category: $("#edit-category").val(),
            location: $("#edit-location").val(),
            price: Number($("#edit-price").val()), // ensure numeric
            status: $("#edit-status").val(),
            user_id: $("#edit-user-id").val() || undefined, // optional for non-admins
            description: $("#edit-description").val(),
            installment_years: parseInt($("#edit-installment-years").val()) || undefined,
            transaction_type: $("#edit-transaction-type").val(),
        };

        // Prepare FormData
        const formData = new FormData();
        Object.keys(payload).forEach((key) => {
            if (payload[key] !== undefined) formData.append(key, payload[key]);
        });

        // Handle single image
        const imageInput = $("#edit-image")[0];
        if (imageInput && imageInput.files.length) {
            formData.append("image", imageInput.files[0]);
        }

        // Handle multiple images
        const multipleImagesInputs = $("#edit-multi-images-wrapper input[type=file]");
        multipleImagesInputs.each(function (i, input) {
            if (input.files.length) formData.append("multiple_images[]", input.files[0]);
        });

        // CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr("content");
        if (csrfToken) formData.append("_token", csrfToken);

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop("disabled", true).text("Saving...");

        // AJAX request
        $.ajax({
            url: `/properties/${propertyId}`,
            type: "POST", // still POST with _method=PUT
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                submitBtn.prop("disabled", false).text(originalText);
            
                const updated = res.property || payload;
            
                // تحديث الصف في الجدول
                const row = $(`#properties-list tbody tr`).filter(function () {
                    return $(this).find("td:first-child").text().trim() === propertyId;
                });
            
                if (row.length) {
                    row.find("td:nth-child(2)").text(updated.category);
                    row.find("td:nth-child(3)").text(updated.location);
                    row.find("td:nth-child(4)").text(Number(updated.price).toLocaleString() + " EGP");
                    row.find("td:nth-child(5)").text(
                        updated.status.charAt(0).toUpperCase() + updated.status.slice(1)
                    );
                    row.find("td:nth-child(6)").text(updated.user_id || "");
            
                    row.data("description", updated.description);
                    row.data("installment", updated.installment_years);
                    row.data("transaction", updated.transaction_type);
                }
            
                closeModal();
                showToast("Property updated successfully!", "success");
            },
            
            error: function (xhr) {
                submitBtn.prop("disabled", false).text(originalText);
                let message = "Error updating property";
                try {
                    const errRes = xhr.responseJSON || JSON.parse(xhr.responseText);
                    if (errRes.errors)
                        message += ": " + Object.values(errRes.errors).flat().join(", ");
                    else if (errRes.message) message += ": " + errRes.message;
                } catch (err) {
                    message += ": " + xhr.statusText;
                }
                showToast(message, "error");
            },
        });

        return false;
    });
});
