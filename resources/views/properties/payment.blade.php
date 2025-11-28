<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EL Kayan - Secure Payment</title>
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="overlay"></div>

    <div class="container">
        <div class="payment-box"
             data-plan="{{ $plan }}"
             data-price="{{ $property->price }}"
             data-years="{{ $property->installment_years }}"
             data-property-id="{{ $property->id }}">
            
            <h1 class="title">EL Kayan</h1>
            <h2 class="subtitle">Complete Your Payment</h2>

            @php
                $downPaymentRate = 0.10;
                $downPayment = $property->price * $downPaymentRate;
                $remainingAmount = $property->price - $downPayment;

                if ($plan === 'annual') {
                    $installments = $property->installment_years;
                    $installmentAmount = $remainingAmount / $installments;
                    $planText = "Annual Installment";
                } elseif ($plan === 'quarterly') {
                    $installments = $property->installment_years * 4;
                    $installmentAmount = $remainingAmount / $installments;
                    $planText = "Quarterly Installment";
                } else { // cash
                    $installments = 1;
                    $installmentAmount = $property->price;
                    $planText = "Full Cash Payment";
                }
            @endphp

            <p class="amount-text">
                @if($plan === 'cash')
                    EGP {{ number_format($property->price) }}
                @else
                    Down Payment: EGP {{ number_format($downPayment) }} <br>
                    {{ $planText }}: EGP {{ number_format($installmentAmount) }} x {{ $installments }}
                @endif
            </p>

            <!-- Card input fields remain -->
            <form id="payment-form" class="payment-form">
                <input id="cardholder" name="cardholder" type="text" placeholder="Cardholder Name" required>
                <span class="error-message" id="name-error"></span>

                <div class="row">
                    <div class="col">
                        <input id="expiry-date" name="expiry_date" type="text" placeholder="Expiration Date (MM/YY)" required>
                        <span class="error-message" id="date-error"></span>
                    </div>
                    <div class="col">
                        <input id="cvv" name="cvv" type="text" placeholder="CVV" required>
                        <span class="error-message" id="cvv-error"></span>
                    </div>
                </div>

                <input id="card-number" name="card_number" type="text" placeholder="Card Number" required>
                <span class="error-message" id="card-error"></span>

                <!-- Removed the original Pay Now button -->
                <div class="paypal-container mt-3">
                    <button type="button" id="paypal-button" class="paypal-btn">
                        <i class="fab fa-paypal"></i> Pay with PayPal
                    </button>
                </div>
            </form>

            <div class="secure-text mt-3">
                <i class="fas fa-lock"></i> Your payment is secured by SSL Encryption
            </div>
        </div>
    </div>

    <script src="{{ asset('js/payment.js') }}"></script>
</body>
</html>
