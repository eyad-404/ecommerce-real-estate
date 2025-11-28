document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const profileUpdateRoute = window.profileUpdateRoute;
    const profileDeleteRoute = window.profileDeleteRoute;
    const profileCheckPasswordRoute = window.profileCheckPasswordRoute;

    const profileForm = document.getElementById('profileForm');
    const profileInput = document.getElementById('profileInput');
    const previewImage = document.getElementById('previewImage');
    const deleteBtnWrapper = document.querySelector('.delete-btn-wrapper');
    const deleteBtn = deleteBtnWrapper ? deleteBtnWrapper.querySelector('button') : null;
    const alertContainer = document.getElementById('alert-container');

    const currentPasswordInput = document.getElementById('current_password');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordIcon = document.getElementById('passwordIcon');
    const passwordText = document.getElementById('passwordText');
    const passwordMatchText = document.getElementById('passwordMatchText');

    if (profileForm) {
        const birthDateInput = profileForm.querySelector('input[name="birth_date"]');
        if (birthDateInput) {
            birthDateInput.setAttribute('max', new Date().toISOString().split('T')[0]);
        }
    }

    function showAlert(message, type = 'success') {
        const div = document.createElement('div');
        div.className = `alert alert-${type}`;
        div.textContent = message;
        alertContainer.prepend(div);
        setTimeout(() => div.remove(), 4000);
    }

    function updateNavAvatars(url) {
        if (!url) return;
        const stampedUrl = `${url}${url.includes('?') ? '&' : '?'}_=${Date.now()}`;
        document.querySelectorAll('.navbar img.profile-img').forEach((img) => {
            img.src = stampedUrl;
        });
    }

    function updatePasswordMatchState() {
        if (!passwordMatchText) return;
        const newVal = newPasswordInput ? newPasswordInput.value : '';
        const confirmVal = confirmPasswordInput ? confirmPasswordInput.value : '';

        if (!newVal && !confirmVal) {
            passwordMatchText.textContent = '';
            passwordMatchText.style.color = '';
            return;
        }

        if (newVal && confirmVal && newVal === confirmVal) {
            passwordMatchText.textContent = 'Passwords match';
            passwordMatchText.style.color = '#10b981';
        } else {
            passwordMatchText.textContent = 'Passwords do not match';
            passwordMatchText.style.color = '#ef4444';
        }
    }

    function showDeleteButton(show) {
        if (deleteBtnWrapper) {
            deleteBtnWrapper.style.display = show ? 'flex' : 'none';
        }
    }

    const savePhotoBtn = document.getElementById('savePhotoBtn');
    let selectedFile = null;

    if (!profileForm || !profileInput || !previewImage || !deleteBtn || !alertContainer || !savePhotoBtn) {
        return;
    }

    showDeleteButton(previewImage.dataset.hasImage === '1');

    // Profile picture selection (preview only)
    profileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (!file) {
            selectedFile = null;
            savePhotoBtn.style.display = 'none';
            return;
        }

        const allowedTypes = ["image/jpeg","image/png","image/jpg","image/webp","image/gif"];
        if (!allowedTypes.includes(file.type)) {
            showAlert('Invalid file type. Only images are allowed.', 'danger');
            profileInput.value = '';
            selectedFile = null;
            savePhotoBtn.style.display = 'none';
            return;
        }

        selectedFile = file;

        const reader = new FileReader();
        reader.onload = () => { 
            previewImage.src = reader.result; 
        };
        reader.readAsDataURL(file);

        savePhotoBtn.style.display = 'block';
    });

    // Save photo button click handler (includes all fields)
    savePhotoBtn.addEventListener('click', () => {
        if (!selectedFile) {
            showAlert('No file selected.', 'danger');
            return;
        }

        savePhotoBtn.disabled = true;
        savePhotoBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Uploading...';

        // Get current form values
        const nameInput = profileForm.querySelector('input[name="name"]');
        const emailInput = profileForm.querySelector('input[name="email"]');
        const phoneInput = profileForm.querySelector('input[name="phone"]');
        const birthDateInput = profileForm.querySelector('input[name="birth_date"]');
        const genderInput = profileForm.querySelector('select[name="gender"]');
        const locationInput = profileForm.querySelector('input[name="location"]');

        const formData = new FormData();
        formData.append('profile_image', selectedFile);
        formData.append('_method', 'PUT');
        formData.append('_token', csrfToken);
        formData.append('name', nameInput ? nameInput.value : '');
        formData.append('email', emailInput ? emailInput.value : '');
        if (phoneInput) formData.append('phone', phoneInput.value || '');
        if (birthDateInput) formData.append('birth_date', birthDateInput.value || '');
        if (genderInput) formData.append('gender', genderInput.value || '');
        if (locationInput) formData.append('location', locationInput.value || '');

        fetch(profileUpdateRoute, { 
            method: 'POST', 
            headers: { 'X-Requested-With': 'XMLHttpRequest' }, 
            body: formData 
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                previewImage.src = data.profile_image;
                showDeleteButton(Boolean(data.has_profile_image));
                showAlert(data.message || 'Profile picture updated!', 'success');
                updateNavAvatars(data.profile_image);

                // Reset inputs
                savePhotoBtn.style.display = 'none';
                selectedFile = null;
                profileInput.value = '';
            } else if (data.errors) {
                Object.values(data.errors).forEach(msgs => msgs.forEach(msg => showAlert(msg, 'danger')));
            }
        })
        .catch(err => {
            console.error(err);
            showAlert('Error uploading photo. Please try again.', 'danger');
        })
        .finally(() => {
            savePhotoBtn.disabled = false;
            savePhotoBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Save Photo';
        });
    });

    // Delete profile picture
    deleteBtn.addEventListener('click', () => {
        if (!confirm('Are you sure you want to delete your profile picture?')) return;

        fetch(profileDeleteRoute, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                previewImage.src = data.profile_image;
                showDeleteButton(Boolean(data.has_profile_image));
                showAlert(data.message || 'Profile picture deleted!', 'success');
                updateNavAvatars(data.profile_image);
            }
        })
        .catch(err => console.error(err));
    });

    // Real-time current password validation
    let passwordTimeout;
    if (currentPasswordInput && passwordIcon && passwordText && profileCheckPasswordRoute) {
        currentPasswordInput.addEventListener('input', () => {
            clearTimeout(passwordTimeout);
            const value = currentPasswordInput.value.trim();
            
            if (!value) {
                passwordIcon.className = '';
                passwordIcon.innerHTML = '';
                passwordText.textContent = '';
                passwordText.style.color = '';
                const passwordFeedback = document.getElementById('passwordFeedback');
                if (passwordFeedback) passwordFeedback.style.display = 'none';
                return;
            }

            const passwordFeedback = document.getElementById('passwordFeedback');
            if (passwordFeedback) passwordFeedback.style.display = 'inline-flex';

            passwordIcon.className = 'fas fa-spinner fa-spin me-1';
            passwordIcon.style.color = '#6b7280';
            passwordText.textContent = 'Checking...';
            passwordText.style.color = '#6b7280';

            passwordTimeout = setTimeout(() => {
                fetch(profileCheckPasswordRoute, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': csrfToken, 
                        'X-Requested-With': 'XMLHttpRequest', 
                        'Content-Type': 'application/json' 
                    },
                    body: JSON.stringify({ current_password: value })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.valid) {
                        passwordIcon.className = 'fas fa-check-circle me-1';
                        passwordIcon.style.color = '#10b981';
                        passwordText.textContent = data.message || 'Password is valid';
                        passwordText.style.color = '#10b981';
                    } else {
                        passwordIcon.className = 'fas fa-times-circle me-1';
                        passwordIcon.style.color = '#ef4444';
                        passwordText.textContent = data.message || 'Password is invalid';
                        passwordText.style.color = '#ef4444';
                    }
                })
                .catch(err => {
                    console.error('Password check error:', err);
                    passwordIcon.className = 'fas fa-exclamation-circle me-1';
                    passwordIcon.style.color = '#f59e0b';
                    passwordText.textContent = 'Error checking password';
                    passwordText.style.color = '#f59e0b';
                });
            }, 500);
        });
    }
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;

            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            button.setAttribute('aria-pressed', showing ? 'false' : 'true');

            const icon = button.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye', showing);
                icon.classList.toggle('bi-eye-slash', !showing);
            }
        });
    });

    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', updatePasswordMatchState);
    }
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', updatePasswordMatchState);
    }
});
