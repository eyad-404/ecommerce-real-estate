const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const emailFeedback = document.getElementById('email-feedback');
const passwordFeedback = document.getElementById('password-feedback');

const isEmailValid = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

const setFieldState = (input, isValid, messageEl, message = '') => {
    if (!input || !messageEl) return;
    input.classList.remove('is-valid', 'is-invalid');
    input.classList.add(isValid ? 'is-valid' : 'is-invalid');
    messageEl.textContent = message;
    messageEl.classList.toggle('success', isValid && !!message);
};

const fetchEmailStatus = async (value) => {
    if (!loginForm) return null;
    const url = loginForm.dataset.checkEmail;
    const token = loginForm.dataset.csrf;
    if (!url || !token) return null;

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({ email: value }),
    });
    return response.json();
};

if (loginForm && emailInput && passwordInput) {
    emailInput.addEventListener('input', async () => {
        const value = emailInput.value.trim();

        if (!value.length) {
            setFieldState(emailInput, false, emailFeedback, '');
            return;
        }

        if (!isEmailValid(value)) {
            setFieldState(emailInput, false, emailFeedback, 'Enter a valid email address.');
            return;
        }

        try {
            const result = await fetchEmailStatus(value);
            if (result?.exists) {
                setFieldState(emailInput, true, emailFeedback, 'Looks good.');
            } else {
                setFieldState(emailInput, false, emailFeedback, 'We could not find an account with this email.');
            }
        } catch (error) {
            console.error('Email check failed', error);
            setFieldState(emailInput, false, emailFeedback, 'Unable to verify email right now.');
        }
    });

    passwordInput.addEventListener('input', () => {
        const value = passwordInput.value;
        if (!value.length) {
            setFieldState(passwordInput, false, passwordFeedback, '');
            return;
        }
        if (value.length < 8) {
            setFieldState(passwordInput, false, passwordFeedback, 'Password must be at least 8 characters.');
        } else {
            setFieldState(passwordInput, true, passwordFeedback, 'Looks good.');
        }
    });

    loginForm.addEventListener('submit', (event) => {
        let isValid = true;
        const emailValue = emailInput.value.trim();

        if (!isEmailValid(emailValue)) {
            setFieldState(emailInput, false, emailFeedback, 'Enter a valid email address.');
            isValid = false;
        }

        if (passwordInput.value.length < 8) {
            setFieldState(passwordInput, false, passwordFeedback, 'Password must be at least 8 characters.');
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
}

const togglePasswordButtons = document.querySelectorAll('[data-password-toggle]');

togglePasswordButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const targetId = button.dataset.passwordToggle;
        const targetInput = document.getElementById(targetId);
        if (!targetInput) return;

        const showing = targetInput.type === 'text';
        targetInput.type = showing ? 'password' : 'text';
        button.setAttribute('aria-pressed', showing ? 'false' : 'true');

        const icon = button.querySelector('i');
        if (icon) {
            icon.classList.toggle('bi-eye', showing);
            icon.classList.toggle('bi-eye-slash', !showing);
        }
    });
});

