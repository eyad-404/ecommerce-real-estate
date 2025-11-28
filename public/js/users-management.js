$(document).ready(function () {
    const todayIso = new Date().toISOString().split('T')[0];
    $('input[name="birth_date"], #edit-birth_date').attr('max', todayIso);

    const allowedRoles = ["admin", "seller", "buyer"];

    function validateUserPayload(payload, options = { requirePassword: true }) {
        const errors = [];

        if (!payload.name || payload.name.trim().length < 3) {
            errors.push("Name must be at least 3 characters.");
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!payload.email || !emailPattern.test(payload.email)) {
            errors.push("Provide a valid email address.");
        }

        if (!payload.phone || !/^\d{10,11}$/.test(payload.phone)) {
            errors.push("Phone number must contain 10 or 11 digits.");
        }

        if (!payload.role || !allowedRoles.includes(payload.role.toLowerCase())) {
            errors.push("Select a valid role.");
        }
        if (!payload.birth_date) {
            errors.push("Birth date is required.");
        } else if (new Date(payload.birth_date) > new Date()) {
            errors.push("Birth date cannot be in the future.");
        }
        if (!payload.gender || !["male", "female", "other"].includes(payload.gender.toLowerCase())) errors.push("Select a valid gender.");
        if (!payload.location || payload.location.trim().length < 2) errors.push("Location is required.");

        const passwordValue = payload.password || "";
        if (options.requirePassword && passwordValue.length < 8) {
            errors.push("Password must be at least 8 characters.");
        } else if (
            !options.requirePassword &&
            passwordValue.length > 0 &&
            passwordValue.length < 8
        ) {
            errors.push("New password must be at least 8 characters.");
        }

        return errors;
    }

    // ------------------- CSRF Setup -------------------
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // ------------------- Toast Notifications -------------------
    function showToast(message, type = "error") {
        const toastContainer = $("#toast-container");
        if (!toastContainer.length) return;

        let iconClass = "fa-exclamation-circle";
        if (type === "success") iconClass = "fa-check-circle";
        else if (type === "warning") iconClass = "fa-exclamation-triangle";
        else if (type === "info") iconClass = "fa-info-circle";

        const toast = $(`
            <div class="toast ${type}">
                <i class="fas ${iconClass} toast-icon"></i>
                <span class="toast-message">${message}</span>
                <button class="toast-close"><i class="fas fa-times"></i></button>
            </div>
        `);

        toastContainer.append(toast);
        toast.find(".toast-close").click(() => toast.remove());
        setTimeout(() => toast.fadeOut(300, () => toast.remove()), 5000);
    }

    // ------------------- Search Users -------------------
    $(".search-bar input").on("keyup", function () {
        const query = $(this).val();
        const url = $(".search-bar").data("route");

        $.ajax({
            url: url,
            type: "GET",
            data: { query: query },
            success: function (users) {
                let tbody = "";
                if (!Array.isArray(users) || users.length === 0) {
                    tbody =
                        '<tr><td colspan="6" style="text-align:center">No users found</td></tr>';
                } else {
                    users.forEach(function (user) {
                        tbody += `<tr data-id="${user.id}">
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.phone}</td>
                            <td>${
                                user.role
                                    ? user.role.charAt(0).toUpperCase() +
                                      user.role.slice(1)
                                    : ""
                            }</td>
                            <td>
                                <button class="btn btn-secondary btn-sm edit-btn"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${
                                    user.id
                                }"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>`;
                    });
                }
                $(".users-table tbody").html(tbody);
            },
            error: function () {
                showToast("Error fetching users", "error");
            },
        });
    });

    // ------------------- Add User -------------------
    $("#add-user-form").submit(function (e) {
        e.preventDefault();
        const form = $(this);

        const payload = {
            name: form.find('input[name="name"]').val(),
            email: form.find('input[name="email"]').val(),
            password: form.find('input[name="password"]').val(),
            phone: form.find('input[name="phone"]').val(),
            role: form.find('select[name="role"]').val(),
            birth_date: form.find('input[name="birth_date"]').val(),
            gender: form.find('select[name="gender"]').val(),
            location: form.find('input[name="location"]').val(),
        };

        const validationErrors = validateUserPayload(payload, {
            requirePassword: true,
        });
        if (validationErrors.length) {
            validationErrors.forEach((err) => showToast(err, "error"));
            return;
        }   

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            dataType: "json",
            data: form.serialize(),
            success: function (response) {
                if (!response || response.success === false) {
                    showToast(response?.message || "Unable to add user.", "error");
                    return;
                }

                const user = response.user || response;
                const row = `<tr data-id="${user.id}" data-birth_date="${user.birth_date || ''}" data-gender="${user.gender || ''}" data-location="${user.location || ''}" data-role="${user.role}">
                <td>${user.id}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.phone}</td>
                <td>${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</td>
                <td>
                    <button class="btn btn-secondary btn-sm edit-btn"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="${user.id}"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
                $(".users-table tbody").append(row);
                form[0].reset();
                showToast("User added successfully!", "success");
            },
            error: function (xhr) {
                const message = xhr.responseJSON?.message || "Unknown error";
                showToast("Error adding user: " + message, "error");
            },
        });
    });

    // ------------------- Delete User -------------------
    // DELETE MODAL
    let deleteUrl = null;
    let rowToDelete = null;

    function openDeleteModal(url, row) {
        deleteUrl = url;
        rowToDelete = row;
        const modal = $("#delete-modal");
        modal.css("display", "flex").css("opacity", 0);
        modal.animate({ opacity: 1 }, 200);
    }

    function closeDeleteModal() {
        deleteUrl = null;
        rowToDelete = null;
        const modal = $("#delete-modal");
        modal.animate({ opacity: 0 }, 200, function () {
            modal.css("display", "none");
        });
    }

    $("#delete-close, #delete-cancel").click(closeDeleteModal);

    $("#delete-form").submit(function (e) {
        e.preventDefault();
        if (deleteUrl && rowToDelete) {
            $.ajax({
                url: deleteUrl,
                type: "POST",
                data: $(this).serialize() + "&_method=DELETE",
                success: function () {
                    rowToDelete.remove();
                    closeDeleteModal();
                    showToast("User deleted successfully!", "success");
                },
                error: function () {
                    showToast("Error deleting user", "error");
                },
            });
        }
    });

    $(document).on("click", ".delete-btn", function () {
        const userId = $(this).data("id");
        const row = $(this).closest("tr");
        openDeleteModal(`/users/${userId}`, row);
    });

    // EDIT MODAL
    function openEditModal(user) {
        $("#edit-user-id").val(user.id);
        $("#edit-name").val(user.name);
        $("#edit-email").val(user.email);
        $("#edit-phone").val(user.phone);
        $("#edit-role").val(user.role.toLowerCase());
        $("#edit-birth_date").val(user.birth_date);
        $("#edit-gender").val(user.gender.toLowerCase());
        $("#edit-location").val(user.location);
        $("#edit-password").val("");
        const modal = $("#edit-modal");
        modal.css("display", "flex").css("opacity", 0);
        modal.animate({ opacity: 1 }, 200);
    }

    function closeEditModal() {
        const modal = $("#edit-modal");
        modal.animate({ opacity: 0 }, 200, function () {
            modal.css("display", "none");
        });
    }

    $("#edit-close, #edit-cancel").click(closeEditModal);

    $(document).on("click", ".edit-btn", function () {
        const row = $(this).closest("tr");
        const user = {
            id: row.data("id"),
            name: row.find("td:nth-child(2)").text().trim(),
            email: row.find("td:nth-child(3)").text().trim(),
            phone: row.find("td:nth-child(4)").text().trim(),
            role: row.data("role") || row.find("td:nth-child(5)").text().trim(),
            birth_date: row.data("birth_date") || "",
            gender: row.data("gender") || "",
            location: row.data("location") || "",
        };
        openEditModal(user);
    });

    $("#edit-user-form").submit(function (e) {
        e.preventDefault();

        const userId = $("#edit-user-id").val();
        const payload = {
            name: $("#edit-name").val(),
            email: $("#edit-email").val(),
            phone: $("#edit-phone").val(),
            role: $("#edit-role").val(),
            birth_date: $("#edit-birth_date").val(),
            gender: $("#edit-gender").val(),
            location: $("#edit-location").val(),
            password: $("#edit-password").val(),
        };

        const errors = validateUserPayload(payload, { requirePassword: false });
        if (errors.length) {
            errors.forEach((err) => showToast(err, "error"));
            return;
        }

        $.ajax({
            url: `/users/${userId}`,
            type: "POST",
            dataType: "json",
            data: {
                _method: "PUT",
                name: payload.name,
                email: payload.email,
                password: payload.password,
                phone: payload.phone,
                role: payload.role,
                birth_date: payload.birth_date,
                gender: payload.gender,
                location: payload.location
            },
            success: function (response) {
                if (!response || response.success === false) {
                    showToast(response?.message || "Failed to update user", "error");
                    return;
                }

                const row = $(".users-table tbody tr").filter(function () {
                    return $(this).data("id") == userId;
                });

                row.find("td:nth-child(2)").text(payload.name);
                row.find("td:nth-child(3)").text(payload.email);
                row.find("td:nth-child(4)").text(payload.phone);
                row.find("td:nth-child(5)").text(
                    payload.role.charAt(0).toUpperCase() + payload.role.slice(1)
                );
                row.data("birth_date", payload.birth_date);
                row.data("gender", payload.gender);
                row.data("location", payload.location);
                row.data("role", payload.role);

                showToast("User updated successfully!", "success");
                closeEditModal();
            },
            error: function (xhr) {
                const message = xhr.responseJSON?.message || "Unknown error";
                showToast("Error updating user: " + message, "error");
            },
        });
    });
});
