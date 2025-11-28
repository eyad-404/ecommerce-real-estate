<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - EL Kayan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
@php($today = now()->toDateString())
<body class="auth-body">

<!-- ================= NAVBAR ================= -->
<nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 text-black" href="{{ url('/') }}">
            <i class="bi bi-building-fill me-1"></i> EL Kayan
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('about-us') ? 'active' : '' }}" href="{{ route('about-us') }}">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('properties') ? 'active' : '' }}" href="{{ route('properties.index') }}">Properties</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-custom btn-sm fw-bold ms-2" href="{{ route('login.form') }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ================= REGISTER FORM ================= -->
<section class="auth-wrapper">
    <div class="auth-card auth-card--wide">
        <div class="auth-card__header">
            <h2>Create your EL Kayan account</h2>
            <p class="helper-text">Fill in your personal details and choose a role to continue.</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-sm mb-3">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-sm mb-3">
                Please fix the highlighted fields before continuing.
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" id="registerForm" novalidate>
            @csrf
            <div class="form-layout">
                <div class="form-section">
                    <div class="input-box">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
                        <small class="validation-msg" id="nameFeedback">@error('name') {{ $message }} @enderror</small>
                    </div>

                    <div class="input-box">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                        <small class="validation-msg" id="emailFeedback">@error('email') {{ $message }} @enderror</small>
                    </div>

                    <div class="input-box">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Enter your phone number" required>
                        <small class="validation-msg" id="phoneFeedback">@error('phone') {{ $message }} @enderror</small>
                    </div>

                    <div class="dual-inputs">
                        <div class="input-box">
                            <label for="birth_date">Birth Date</label>
                            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" required max="{{ $today }}">
                            <small class="validation-msg" id="birthFeedback">@error('birth_date') {{ $message }} @enderror</small>
                        </div>
                        <div class="input-box">
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender" required>
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            <small class="validation-msg" id="genderFeedback">@error('gender') {{ $message }} @enderror</small>
                        </div>
                    </div>

                    <div class="input-box">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="Enter your location" required>
                        <small class="validation-msg" id="locationFeedback">@error('location') {{ $message }} @enderror</small>
                    </div>

                    <div class="dual-inputs">
                        <div class="input-box">
                            <label for="password">Password</label>
                            <div class="password-field">
                                <input type="password" name="password" id="password" placeholder="Enter your password" required minlength="8">
                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-password-toggle="password"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="validation-msg" id="passwordFeedback">@error('password') {{ $message }} @enderror</small>
                        </div>
                        <div class="input-box">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="password-field">
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm your password" required>
                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-password-toggle="password_confirmation"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="validation-msg" id="confirmFeedback"></small>
                        </div>
                    </div>

                    <div class="input-box">
                        <label for="role">Role</label>
                        <select name="role" id="role" required>
                            <option disabled value="">Select role</option>
                            <option value="buyer" {{ old('role') === 'buyer' ? 'selected' : '' }}>Buyer</option>
                            <option value="seller" {{ old('role') === 'seller' ? 'selected' : '' }}>Seller</option>
                            <option value="developer" {{ old('role') === 'developer' ? 'selected' : '' }}>Developer</option>
                        </select>
                        <small class="validation-msg" id="roleFeedback">@error('role') {{ $message }} @enderror</small>
                    </div>

                    <button type="submit" class="login-btn">Sign Up</button>
                </div>

                <div class="requirements-card">
                    <p class="requirements-title">Requirements (update as you type)</p>
                    <ul class="requirements-list" id="requirementsList">
                        <li data-rule="name"><span class="status-icon">•</span>Full name is at least 3 characters</li>
                        <li data-rule="email"><span class="status-icon">•</span>Valid email address</li>
                        <li data-rule="phone"><span class="status-icon">•</span>Phone number has 10 or 11 digits</li>
                        <li data-rule="birth_date"><span class="status-icon">•</span>Birth date selected</li>
                        <li data-rule="gender"><span class="status-icon">•</span>Gender selected</li>
                        <li data-rule="location"><span class="status-icon">•</span>Location entered</li>
                        <li data-rule="password"><span class="status-icon">•</span>Password ≥ 8 characters</li>
                        <li data-rule="confirm"><span class="status-icon">•</span>Passwords match</li>
                        <li data-rule="role"><span class="status-icon">•</span>Role selected</li>
                    </ul>
                </div>
            </div>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="{{ route('login.form') }}">Log in</a></p>
        </div>
    </div>
</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="{{ asset('js/register.js') }}"></script>

</body>
</html>
