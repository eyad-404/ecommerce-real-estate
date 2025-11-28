<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Management - EL Kayan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/property-management.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
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
                    <a class="nav-link fw-semibold {{ Request::is('properties') ? 'active' : '' }}" href="{{ route('properties.index') }}">Properties</a>
                </li>
                
                    <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                
                @auth
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link fw-semibold {{ Request::is('users-management') ? 'active' : '' }}" href="{{ route('users-management') }}">User Management</a>
                        </li>
                    @endif
                @endauth
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('property-management') ? 'active' : '' }}" href="{{ route('property-management') }}">Property Management</a>
                </li>
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="{{ Auth::user()->profile_image_url }}" 
                             alt="{{ Auth::user()->name }}" 
                             class="rounded-circle profile-img me-2">
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
@php $canAssignOwner = $canAssignOwner ?? false; @endphp
<div id="toast-container"></div>
<div class="users-management-container">

    <!-- ===================== LEFT PANEL: Add Property Form ===================== -->
    <div class="user-info-panel">
        <div class="panel-title">Add Property</div>
        <form id="add-property-form" action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data" class="field-list">
            @csrf
            <div class="field-label">Category</div>
            <div class="field-control"><input type="text" name="category" placeholder="e.g., Apartment" required></div>

            <div class="field-label">Location</div>
            <div class="field-control"><input type="text" name="location" placeholder="e.g., Maadi" required></div>
            
            <div class="field-label">Price</div>
            <div class="field-control"><input type="number" name="price" placeholder="1000000" min="0" required></div>

            <div class="field-label">Transaction Type</div>
            <select name="transaction_type" id="transaction_type" class="form-select">
                <option value="sale" {{ old('transaction_type', $property->transaction_type ?? '') == 'sale' ? 'selected' : '' }}>For Sale</option>
                <option value="rent" {{ old('transaction_type', $property->transaction_type ?? '') == 'rent' ? 'selected' : '' }}>For Rent</option>
            </select>

            <div class="field-label">Status</div>
            <div class="field-control">
                <select name="status" required>
                    <option value="" disabled selected>Select Status</option>
                    <option value="available">Available</option>
                    <option value="sold">Sold</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            <div class="field-label">Description</div>
            <div class="field-control"><textarea name="description" rows="3" placeholder="Property description"></textarea></div>

            <div class="field-label">Installment Years</div>
            <div class="field-control"><input type="number" name="installment_years" min="0" placeholder="0"></div>

            <div class="field-label">Property Image</div>
            <div class="field-control"><input type="file" id="property-image" name="image" accept="image/*"></div>

            <div class="field-label d-flex justify-content-between align-items-center">
                <span>Additional Images</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-multi-image-btn">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="field-control multi-images-wrapper" id="multi-images-wrapper">
                <input type="file" class="form-control mb-2" name="multiple_images[]" accept="image/*">
            </div>

            @if($canAssignOwner)
                <div class="field-label">User ID</div>
                <div class="field-control"><input type="number" name="user_id" placeholder="Owner User ID" min="1"></div>
            @else
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            @endif

            <div class="button-group">
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Property</button>
            </div>
        </form>
    </div>

    <!-- ===================== RIGHT PANEL: Properties Table ===================== -->
    <div class="users-list-panel">
        <div class="users-list-header">
            <h2 class="panel-title mb-0">Property Lists</h2>
            <form class="search-bar">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search properties by category, location, or ID...">
            </form>
        </div>
        <div class="users-table-container" id="properties-list">
            <table class="users-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>User ID</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($properties as $property)
                    <tr data-description="{{ $property->description }}" data-installment="{{ $property->installment_years }}" data-transaction="{{ $property->transaction_type }}">
                        <td>{{ $property->id }}</td>
                        <td>{{ $property->category }}</td>
                        <td>{{ $property->location }}</td>
                        <td>{{ number_format($property->price) }} EGP</td>
                        <td>{{ ucfirst($property->status) }}</td>
                        <td>{{ $property->user_id }}</td>
                        <td>
                            <button class="btn btn-secondary btn-sm edit-btn" data-id="{{ $property->id }}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $property->id }}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ===================== MODALS ===================== -->
@include('property-management.edit-modal')
@include('property-management.delete-modal')

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('js/property-management.js') }}"></script>
<script src="{{ asset('js/property-management/edit-modal.js') }}"></script>
<script src="{{ asset('js/property-management/delete-modal.js') }}"></script>

</body>
</html>
