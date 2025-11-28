document.addEventListener('DOMContentLoaded', function () {
  // Toast notification function
  function showToast(message, type = 'error') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    // Icon based on type
    let iconClass = 'fa-exclamation-circle';
    if (type === 'success') iconClass = 'fa-check-circle';
    else if (type === 'warning') iconClass = 'fa-exclamation-triangle';
    else if (type === 'info') iconClass = 'fa-info-circle';

    toast.innerHTML = `
      <i class="fas ${iconClass} toast-icon"></i>
      <span class="toast-message">${message}</span>
      <button class="toast-close" aria-label="Close">
        <i class="fas fa-times"></i>
      </button>
    `;

    toastContainer.appendChild(toast);

    // Close button functionality
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => {
      removeToast(toast);
    });

    // Auto remove after 5 seconds
    setTimeout(() => {
      removeToast(toast);
    }, 5000);
  }

  function removeToast(toast) {
    toast.classList.add('hiding');
    setTimeout(() => {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 300);
  }

  // Validation functions
  function validateEmail(email) {
    return email.includes('@gmail.com');
  }

  function validatePhone(phone) {
    // Check if phone contains only numbers (allowing + at the start)
    return /^\+?[0-9]+$/.test(phone);
  }

  function isStrongPassword(password) {
    // Must include lowercase, uppercase, number, and special char
    const strongPasswordPattern =
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    return strongPasswordPattern.test(password);
  }

  // Inputs
  const fullNameEl = document.getElementById('full-name');
  const emailEl = document.getElementById('email');
  const passwordEl = document.getElementById('password');
  const phoneEl = document.getElementById('phone');
  const roleEl = document.getElementById('role');

  const addUserBtn = document.getElementById('add-user-btn');
  const participantsList = document.getElementById('participants-list');
  const editUserBtn = document.getElementById('edit-user-btn');
  let selectedUser = null;

  // Check if required elements exist
  if (!addUserBtn) {
    console.error('Add user button not found');
    return;
  }
  if (!participantsList) {
    console.error('Participants list not found');
    return;
  }

  // Modal
  const modalOverlay = document.getElementById('modal-overlay');
  const modalClose = document.getElementById('modal-close');
  const modalCancel = document.getElementById('modal-cancel');
  const modalConfirm = document.getElementById('modal-confirm');
  let onModalConfirm = null;

  // Modal handling
  function openModal(onConfirm) {
    onModalConfirm = onConfirm;
    if (modalOverlay) modalOverlay.hidden = false;
  }
  function closeModal() {
    if (modalOverlay) modalOverlay.hidden = true;
    onModalConfirm = null;
  }
  if (modalClose) modalClose.addEventListener('click', closeModal);
  if (modalCancel) modalCancel.addEventListener('click', closeModal);
  if (modalConfirm) modalConfirm.addEventListener('click', function () {
    if (typeof onModalConfirm === 'function') onModalConfirm();
    closeModal();
  });

  // Add user
  addUserBtn.addEventListener('click', function () {
    const fullName = fullNameEl.value.trim();
    const email = emailEl.value.trim();
    const phone = phoneEl.value.trim();
    const role = roleEl.value;
    const password = passwordEl ? passwordEl.value.trim() : '';

    // Check if any field is empty
    if (!fullName || !email || !password || !phone || !role) {
      showToast('Please fill in all fields before adding a user', 'warning');
      return;
    }

    // Validate email
    if (!validateEmail(email)) {
      showToast('Email must contain @gmail', 'error');
      return;
    }

    // Validate phone
    if (!validatePhone(phone)) {
      showToast('Phone number must contain only numbers', 'error');
      return;
    }

    if (!isStrongPassword(password)) {
      showToast(
        'Password must be at least 8 characters with upper, lower, number & symbol.',
        'error'
      );
      return;
    }

    const item = document.createElement('div');
    item.className = 'participants-item';
    item.innerHTML = `
      <div class="participant-info">
        <strong>${fullName}</strong>
        <span>${email} · ${phone}</span>
      </div>
      <div class="participant-actions">
        <span class="participant-role">${role}</span>
        <button class="icon-btn delete-item" title="Delete"><i class="fas fa-trash"></i></button>
      </div>
    `;
    participantsList.appendChild(item);

    // Clear any selected user if editing
    if (selectedUser) {
      selectedUser.classList.remove('selected');
      selectedUser = null;
      editUserBtn.disabled = true;
    }

    // Clear form fields
    fullNameEl.value = '';
    emailEl.value = '';
    phoneEl.value = '';
    if (passwordEl) passwordEl.value = '';
    roleEl.selectedIndex = 0; // Reset to first option (Select Role)
    showToast('User added successfully!', 'success');
  });

  // Delete participants
  participantsList.addEventListener('click', function (e) {
    const btn = e.target.closest('.delete-item');
    if (!btn) return;
    const item = btn.closest('.participants-item');
    if (!item) return;
    openModal(function () {
      item.remove();
      if (selectedUser === item) {
        selectedUser = null;
        editUserBtn.disabled = true;
        fullNameEl.value = '';
        emailEl.value = '';
        phoneEl.value = '';
        if (passwordEl) passwordEl.value = '';
        roleEl.selectedIndex = 0;
      }
    });
  });

  // --- Search for Users Only ---
  const searchInput = document.getElementById('user-search');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const searchValue = this.value.toLowerCase();
      const users = participantsList.querySelectorAll('.participants-item');
      users.forEach(user => {
        const name = user.querySelector('strong').textContent.toLowerCase();
        const details = user.querySelector('span').textContent.toLowerCase();
        user.style.display = (name.includes(searchValue) || details.includes(searchValue)) ? 'flex' : 'none';
      });
    });
  }

  // --- Edit User Functionality ---
  // Handle user selection
  participantsList.addEventListener('click', function (e) {
    const userItem = e.target.closest('.participants-item');
    if (!userItem || e.target.closest('.delete-item')) return;

    // Deselect if clicking the same user
    if (selectedUser === userItem) {
      userItem.classList.remove('selected');
      selectedUser = null;
      editUserBtn.disabled = true;
      return;
    }

    // Remove previous selection
    if (selectedUser) {
      selectedUser.classList.remove('selected');
    }

    // Select new user
    selectedUser = userItem;
    userItem.classList.add('selected');
    editUserBtn.disabled = false;

    // Get user data
    const name = userItem.querySelector('strong').textContent;
    const [email, phone] = userItem.querySelector('span').textContent.split(' · ');
    const role = userItem.querySelector('.participant-role').textContent;

    // Fill form with user data
    fullNameEl.value = name;
    emailEl.value = email;
    phoneEl.value = phone;
    roleEl.value = role;
  });

  // Handle edit button click
  editUserBtn.addEventListener('click', function () {
    if (!selectedUser) return;

    const name = fullNameEl.value.trim();
    const email = emailEl.value.trim();
    const phone = phoneEl.value.trim();
    const role = roleEl.value;

    if (!name || !email || !phone || !role) return;

    // Validate email
    if (!validateEmail(email)) {
      showToast('Email must contain @gmail', 'error');
      return;
    }

    // Validate phone
    if (!validatePhone(phone)) {
      showToast('Phone number must contain only numbers', 'error');
      return;
    }

    // Update user item with new data
    selectedUser.querySelector('strong').textContent = name;
    selectedUser.querySelector('span').textContent = `${email} · ${phone}`;
    selectedUser.querySelector('.participant-role').textContent = role;

    // Clear selection and form
    selectedUser.classList.remove('selected');
    selectedUser = null;
    editUserBtn.disabled = true;

    // Clear form
    fullNameEl.value = '';
    emailEl.value = '';
    phoneEl.value = '';
    roleEl.selectedIndex = 0;
    showToast('User updated successfully!', 'success');
  });
});

