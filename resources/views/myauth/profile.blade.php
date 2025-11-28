<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - EL Kayan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@php
    $authUser = auth()->user();
    $profileImageUrl = $authUser->profile_image_url;
    $hasProfileImage = optional($authUser->profile)->profile_image ? '1' : '0';
@endphp
<body class="d-flex flex-column min-vh-100">

<!-- ================= NAVBAR ================= -->
<nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="{{ url('/') }}">
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
                    <a class="nav-link fw-semibold {{ Request::is('properties') ? 'active' : '' }}" href="{{ route('properties.index') }}">
                        Properties
                    </a>
                </li>
                @auth
                    @auth
                        @if(in_array(auth()->user()->role, ['admin', 'seller']))
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                        @endif
                    @endauth

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="{{ Auth::user()->profile_image_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle profile-img me-2">
                        <span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item d-flex align-items-center" href="{{ route('profile') }}"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-custom btn-sm fw-bold ms-2" href="{{ route('login.form') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- ================= MAIN CONTENT ================= -->
<div class="container profile-page py-5" style="margin-top: 80px;">
    <div id="alert-container"></div>

    <div class="row g-4">
        <!-- LEFT: Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="mb-4"><i class="bi bi-person-circle me-2"></i>Personal Information</h5>
                <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Name & Email -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-person me-1"></i>Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-envelope me-1"></i>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-telephone me-1"></i>Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}">
                        </div>

                        <!-- Birth Date -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-calendar me-1"></i>Birth Date</label>
                            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', auth()->user()->birth_date) }}" max="{{ now()->toDateString() }}">
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-gender-ambiguous me-1"></i>Gender</label>
                            <select name="gender" class="form-select">
                                <option value="" disabled {{ old('gender', auth()->user()->gender) ? '' : 'selected' }}>Select Gender</option>
                                <option value="male" {{ old('gender', auth()->user()->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', auth()->user()->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', auth()->user()->gender) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Location -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-geo-alt me-1"></i>Location</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', auth()->user()->location) }}">
                        </div>

                        <!-- Current Password -->
                        <div class="col-md-6">
                            <label class="form-label d-flex align-items-center">
                                <i class="bi bi-lock me-1"></i>Current Password
                                <span id="passwordFeedback" class="ms-2 d-flex align-items-center">
                                    <span id="passwordIcon"></span>
                                    <span id="passwordText" style="font-size: 0.875rem; font-weight: 500;"></span>
                                </span>
                            </label>
                            <div class="password-field">
                                <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Enter current password">
                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-password-toggle="current_password"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- New Password & Confirm -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-key me-1"></i>New Password</label>
                            <div class="password-field">
                                <input type="password" name="password" id="new_password" class="form-control" placeholder="Leave empty to keep current">
                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-password-toggle="new_password"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-key-fill me-1"></i>Confirm New Password</label>
                            <div class="password-field">
                                <input type="password" name="password_confirmation" id="confirm_password" class="form-control" placeholder="Confirm new password">
                                <button
                                    type="button"
                                    class="password-toggle"
                                    data-password-toggle="confirm_password"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small id="passwordMatchText" class="form-text"></small>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-check-circle me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT: Profile Picture -->
        <div class="col-lg-4">
            <div class="card shadow-sm p-4 text-center">
                <h5 class="mb-4"><i class="bi bi-image me-2"></i>Profile Picture</h5>
                <div class="profile-container mx-auto mb-3">
                    <img id="previewImage"
                        src="{{ $profileImageUrl }}"
                        data-has-image="{{ $hasProfileImage }}"
                        alt="Profile Picture" class="rounded-circle profile-img-large">

                    <div class="profile-overlay">
                        <i class="bi bi-camera-fill mb-1"></i>
                        <span>Upload</span>
                        <input id="profileInput" type="file" name="profile_image" accept="image/*">
                    </div>
                </div>

                <div class="profile-picture-actions">
                    <button type="button" id="savePhotoBtn" class="btn btn-primary btn-sm mb-2" style="display: none;">
                        <i class="bi bi-check-circle me-1"></i>Save Photo
                    </button>
                    <div class="delete-btn-wrapper">
                        <button type="button" class="btn btn-sm delete-btn">
                            <i class="bi bi-trash me-1"></i>Remove Picture
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.profileUpdateRoute = '{{ route("profile.update") }}';
    window.profileDeleteRoute = '{{ route("profile.deletePic") }}';
    window.profileCheckPasswordRoute = '{{ route("profile.checkPassword") }}';
</script>
<script src="{{ asset('js/profile.js') }}"></script>
</body>
</html>
