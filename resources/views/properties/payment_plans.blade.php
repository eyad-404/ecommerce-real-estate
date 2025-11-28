{{-- resources/views/properties/payment_plans.blade.php --}}

@include('includes.header')

<link rel="stylesheet" href="{{ asset('assets/details.css') }}">

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('properties.show', $property->id) }}" class="btn btn-secondary">← Back to Details</a>
</div>

<h1 class="text-primary border-bottom border-secondary pb-2 mb-4">
    Payment Plans for: {{ $property->title }}
</h1>

<p class="fs-5">
    <strong>Total Price:</strong> 
    <span class="text-warning">{{ number_format($property->price) }} EGP</span>
</p>
<p class="mb-5 text-muted">
    Maximum Installment Period: {{ $property->installment_years }} Years
</p>

<div class="row row-cols-1 row-cols-md-3 g-4">

    {{-- Annual Plan --}}
    <div class="col">
        <div class="card bg-dark text-white h-100 border-primary shadow-lg">
            <div class="card-header bg-primary text-white text-center fs-5 fw-bold">
                Annual Plan (Yearly)
            </div>
            <div class="card-body d-flex flex-column">

                @php
                    $down_payment_rate = 0.10;
                    $down_payment_amount = $property->price * $down_payment_rate;
                    $remaining_amount = $property->price - $down_payment_amount;
                    $annual_installments = $property->installment_years;
                    $annual_payment_amount = $remaining_amount / $annual_installments;
                @endphp

                <h3 class="card-title fs-4 text-warning mb-3">Initial Down Payment (10%)</h3>
                <p class="fs-2 fw-bold text-success border-bottom border-secondary pb-2 mb-3">
                    {{ number_format($down_payment_amount) }} EGP
                </p>

                <p class="text-muted">Remaining amount to be paid over {{ $annual_installments }} years.</p>

                <div class="mt-3">
                    <h4 class="fs-5">Annual Installment Amount:</h4>
                    <p class="fs-3 fw-bold text-info">
                        {{ number_format($annual_payment_amount) }} EGP / Year
                    </p>
                    <p class="text-muted small">(Total {{ $annual_installments }} payments)</p>
                </div>

                <a href="{{ route('payment.process', ['property' => $property->id, 'plan' => 'annual']) }}" 
                   class="btn btn-success mt-auto fw-bold" data-plan="annual">Go to Payment</a>
            </div>
        </div>
    </div>

    {{-- Quarterly Plan --}}
    <div class="col">
        <div class="card bg-dark text-white h-100 border-primary shadow-lg">
            <div class="card-header bg-primary text-white text-center fs-5 fw-bold">
                Quarterly Plan
            </div>
            <div class="card-body d-flex flex-column">

                @php
                    $quarterly_installments = $property->installment_years * 4;
                    $quarterly_payment_amount = $remaining_amount / $quarterly_installments;
                @endphp

                <h3 class="card-title fs-4 text-warning mb-3">Initial Down Payment (10%)</h3>
                <p class="fs-2 fw-bold text-success border-bottom border-secondary pb-2 mb-3">
                    {{ number_format($down_payment_amount) }} EGP
                </p>

                <p class="text-muted">Remaining amount to be paid over {{ $property->installment_years }} years.</p>

                <div class="mt-3">
                    <h4 class="fs-5">Quarterly Installment Amount:</h4>
                    <p class="fs-3 fw-bold text-info">
                        {{ number_format($quarterly_payment_amount) }} EGP / Quarter
                    </p>
                    <p class="text-muted small">(Total {{ $quarterly_installments }} payments)</p>
                </div>

                <a href="{{ route('payment.process', ['property' => $property->id, 'plan' => 'quarterly']) }}" 
                   class="btn btn-success mt-auto fw-bold" data-plan="quarterly">Go to Payment</a>
            </div>
        </div>
    </div>

    {{-- Cash Plan --}}
    <div class="col">
        <div class="card bg-dark text-white h-100 border-success shadow-lg">
            <div class="card-header bg-success text-white text-center fs-5 fw-bold">
                Cash Payment Option
            </div>
            <div class="card-body d-flex flex-column">
                <h3 class="card-title fs-4 text-white mb-3">One-Time Full Amount:</h3>
                <p class="fs-1 fw-bold text-warning border-bottom border-secondary pb-2 mb-3">
                    {{ number_format($property->price) }} EGP
                </p>

                <p class="text-danger fw-bold">No interest or installment fees apply</p>
                <p class="text-muted">This option represents the full payment for the property upon contract signing.</p>

                <div style="flex-grow: 1;"></div> 

                <a href="{{ route('payment.process', ['property' => $property->id, 'plan' => 'cash']) }}" 
                   class="btn btn-success mt-auto fw-bold" data-plan="cash">Go to Payment</a>
            </div>
        </div>
    </div>

</div>

@include('includes.footer')
