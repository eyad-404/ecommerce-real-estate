const registerForm = document.getElementById('registerForm');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const phoneInput = document.getElementById('phone');
const birthInput = document.getElementById('birth_date');
const genderInput = document.getElementById('gender');
const locationInput = document.getElementById('location');
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('password_confirmation');
const roleInput = document.getElementById('role');

const todayIso = new Date().toISOString().split('T')[0];
if (birthInput) {
    birthInput.setAttribute('max', todayIso);
}

const validators = {
    name: (value) => value.trim().length >= 3,
    email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
    phone: (value) => /^\d{10,11}$/.test(value),
    birth_date: (value) => !!value && new Date(value) <= new Date(),
    gender: (value) => !!value,
    location: (value) => value.trim().length > 0,
    password: (value) => value.length >= 8,
    confirm: (value) => value === passwordInput.value && value.length >= 8,
    role: (value) => !!value,
};

const messages = {
    name: 'Name must be at least 3 characters.',
    email: 'Enter a valid email address.',
    phone: 'Phone must be 10 or 11 digits.',
    birth_date: 'Select a valid birth date that is not in the future.',
    gender: 'Select a gender.',
    location: 'Enter your location.',
    password: 'Password must be at least 8 characters.',
    confirm: 'Passwords must match.',
    role: 'Select a role.',
};

const requirementItems = {};
document.querySelectorAll('#requirementsList li').forEach((item) => {
    requirementItems[item.dataset.rule] = item;
});

const setInputState = (element, isValid) => {
    if (!element) return;
    element.classList.remove('is-valid', 'is-invalid');
    element.classList.add(isValid ? 'is-valid' : 'is-invalid');
};

const setRequirementStatus = (rule, isValid) => {
    const item = requirementItems[rule];
    if (!item) return;
    const icon = item.querySelector('.status-icon');
    item.classList.remove('valid', 'invalid');
    item.classList.add(isValid ? 'valid' : 'invalid');
    if (icon) icon.textContent = isValid ? '✔' : '•';
};

Object.keys(requirementItems).forEach((rule) => setRequirementStatus(rule, false));

function validateField(element, key) {
    const isValid = validators[key](element.value);
    const feedback = document.getElementById(`${key}Feedback`);
    if (feedback) {
        feedback.textContent = isValid ? 'Looks good' : messages[key];
        feedback.style.color = isValid ? 'green' : 'red';
    }
    if (element) setInputState(element, isValid);
    if (requirementItems[key]) setRequirementStatus(key, isValid);
    if (key === 'confirm') setRequirementStatus('confirm', isValid);
    return isValid;
}

[nameInput, emailInput, phoneInput, birthInput, genderInput, locationInput, passwordInput, confirmInput].forEach((input) => {
    if (!input) return;
    const key = input.id === 'password_confirmation' ? 'confirm' : input.id;
    input.addEventListener('input', () => validateField(input, key));
});

if (roleInput) {
    roleInput.addEventListener('change', () => setRequirementStatus('role', validators.role(roleInput.value)));
}

registerForm.addEventListener('submit', function (e) {
    let isValid = true;
    [nameInput, emailInput, phoneInput, birthInput, genderInput, locationInput, passwordInput, confirmInput].forEach((input) => {
        const key = input.id === 'password_confirmation' ? 'confirm' : input.id;
        isValid = validateField(input, key) && isValid;
    });
    isValid = validators.role(roleInput.value) && isValid;
    setRequirementStatus('role', validators.role(roleInput.value));

    if (!isValid) e.preventDefault();
});

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const target = document.getElementById(button.dataset.passwordToggle);
        if (!target) return;

        const showing = target.type === 'text';
        target.type = showing ? 'password' : 'text';
        button.setAttribute('aria-pressed', showing ? 'false' : 'true');

        const icon = button.querySelector('i');
        if (icon) {
            icon.classList.toggle('bi-eye', showing);
            icon.classList.toggle('bi-eye-slash', !showing);
        }
    });
});
