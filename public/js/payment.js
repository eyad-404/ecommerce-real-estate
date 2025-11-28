document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('payment-form');
    const cardNumberInput = document.getElementById('card-number');
    const expiryDateInput = document.getElementById('expiry-date');
    const cvvInput = document.getElementById('cvv');
    const cardholderInput = document.getElementById('cardholder');
    const payButton = document.getElementById('pay-button');
    const paypalButton = document.getElementById('paypal-button');

    // Get dynamic values from data attributes
    const paymentBox = document.querySelector('.payment-box');
    const plan = paymentBox.dataset.plan;
    const propertyPrice = parseFloat(paymentBox.dataset.price);
    const installmentYears = parseInt(paymentBox.dataset.years);

    const downPayment = propertyPrice * 0.10;
    let installmentAmount;
    let totalAmount;

    if(plan === 'annual') {
        installmentAmount = (propertyPrice - downPayment) / installmentYears;
        totalAmount = downPayment + installmentAmount;
    } else if(plan === 'quarterly') {
        const quarters = installmentYears * 4;
        installmentAmount = (propertyPrice - downPayment) / quarters;
        totalAmount = downPayment + installmentAmount;
    } else { // cash
        installmentAmount = propertyPrice;
        totalAmount = propertyPrice;
    }

    // Update amount text and button
    document.querySelector('.amount-text').innerHTML =
        plan === 'cash' 
            ? 'EGP ' + propertyPrice.toLocaleString() 
            : 'Down Payment: EGP ' + downPayment.toLocaleString() + '<br>' +
              (plan === 'annual' ? 'Annual Installment: ' : 'Quarterly Installment: ') +
              'EGP ' + installmentAmount.toLocaleString();

    payButton.innerHTML = `<i class="fas fa-lock"></i> Pay EGP ${totalAmount.toLocaleString()}`;

    // Card input formatting
    cardNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) formattedValue += ' ';
            formattedValue += value[i];
        }
        e.target.value = formattedValue;
    });

    expiryDateInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
        } else {
            e.target.value = value;
        }
    });

    cvvInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });

    // Form submission (mock)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            payButton.disabled = true;
            setTimeout(function() {
                alert('Payment Successful! Thank you for your purchase.');
                form.reset();
                payButton.innerHTML = `<i class="fas fa-lock"></i> Pay EGP ${totalAmount.toLocaleString()}`;
                payButton.disabled = false;
            }, 2000);
        }
    });

    // PayPal button redirect
    paypalButton.addEventListener('click', function() {
        window.location.href = "/paypal/create"; // adjust route if needed
    });

    // --- Validation functions ---
    function validateForm() {
        let isValid = true;
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        document.querySelectorAll('input').forEach(input => input.classList.remove('shake'));

        if (!validateName(cardholderInput.value)) {
            document.getElementById('name-error').textContent = 'Please enter a valid name (letters and spaces only)';
            cardholderInput.classList.add('shake');
            isValid = false;
        }

        if (!validateCardNumber(cardNumberInput.value)) {
            document.getElementById('card-error').textContent = 'Please enter a valid 16-digit card number';
            cardNumberInput.classList.add('shake');
            isValid = false;
        }

        if (!validateExpiryDate(expiryDateInput.value)) {
            document.getElementById('date-error').textContent = 'Please enter a valid future date (MM/YY)';
            expiryDateInput.classList.add('shake');
            isValid = false;
        }

        if (!validateCVV(cvvInput.value)) {
            document.getElementById('cvv-error').textContent = 'Please enter a valid 3-digit CVV';
            cvvInput.classList.add('shake');
            isValid = false;
        }

        return isValid;
    }

    function validateName(name) {
        return /^[a-zA-Z\s]+$/.test(name) && name.length > 0;
    }

    function validateCardNumber(number) {
        return /^\d{16}$/.test(number.replace(/\s/g, ''));
    }

    function validateExpiryDate(date) {
        const regex = /^(0[1-9]|1[0-2])\/([0-9]{2})$/;
        if (!regex.test(date)) return false;
        const [month, year] = date.split('/').map(Number);
        const now = new Date();
        const currentYear = now.getFullYear() % 100;
        const currentMonth = now.getMonth() + 1;
        return year > currentYear || (year === currentYear && month >= currentMonth);
    }

    function validateCVV(cvv) {
        return /^\d{3}$/.test(cvv);
    }
});
