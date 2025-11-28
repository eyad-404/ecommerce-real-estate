<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EL Kayan</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
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
                    <a class="btn btn-custom btn-sm fw-bold ms-2" href="{{ route('register.form') }}">
                        <i class="bi bi-person-plus me-1"></i> Sign Up
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ================= LOGIN FORM ================= -->
<section class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-card__header">
            <h2>Welcome back</h2>
            <p class="helper-text">Sign in to manage your listings, reservations and analytics.</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-sm mb-3">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-sm mb-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form
            action="{{ route('login') }}"
            method="POST"
            id="loginForm"
            novalidate
            data-check-email="{{ route('check.email') }}"
            data-csrf="{{ csrf_token() }}"
        >
            @csrf
            <div class="input-box">
                <label for="email">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    placeholder="Enter your email"
                    required
                >
                <small id="email-feedback" class="validation-msg">
                    @error('email') {{ $message }} @enderror
                </small>
            </div>

            <div class="input-box">
                <label for="password">Password</label>
                <div class="password-field">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        required
                        minlength="8"
                    >
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
                <small id="password-feedback" class="validation-msg">
                    @error('password') {{ $message }} @enderror
                </small>
            </div>

            <div class="auth-meta">
                <a href="{{ route('password.request') }}">Forgot password?</a>
            </div>

            <button type="submit" class="login-btn w-100">Login</button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="{{ route('register.form') }}">Create one</a></p>
        </div>
    </div>
</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- AJAX Script -->
<script src="{{ asset('js/login.js') }}"></script>

</body>
</html>
