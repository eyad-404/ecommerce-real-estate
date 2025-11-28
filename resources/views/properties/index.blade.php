<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Properties - EL Kayan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/properties-index.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

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
                    <a class="nav-link fw-semibold {{ Request::is('properties') ? 'active' : '' }}" href="{{ route('properties.index') }}">Properties</a>
                </li>
                @auth
                @if(in_array(auth()->user()->role, ['admin', 'seller']))
                <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
                @endif
                @endauth
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

<!-- ================= PAGE CONTENT ================= -->
<div class="container-fluid mt-5 pt-4">

    {{-- ================= SEARCH & FILTER FORM ================= --}}
    <div class="filter-container mb-4 p-4 shadow-lg rounded-4 bg-light">
        <form class="row g-3" method="GET" action="{{ route('properties.index') }}" data-bs-theme="light">

            {{-- Buy/Rent Filter --}}
            <div class="col-md-3 col-lg-2">
                <label for="transaction_type" class="form-label">Transaction Type</label>
                <select id="transaction_type" name="transaction_type" class="form-select">
                    <option value="">All</option>
                    <option value="sale" @selected(request('transaction_type') === 'sale')>For Sale</option>
                    <option value="rent" @selected(request('transaction_type') === 'rent')>For Rent</option>
                </select>
            </div>

            {{-- Search Term --}}
            <div class="col-md-4 col-lg-3">
                <label for="search_term" class="form-label">Search (Category or Location)</label>
                <input type="text" id="search_term" name="search_term" class="form-control" value="{{ request('search_term') }}" placeholder="e.g., Apartment or Maadi">
            </div>

            {{-- Category Filter --}}
            <div class="col-md-4 col-lg-2">
                <label for="category" class="form-label">Category</label>
                <select id="category" name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Location Filter --}}
            <div class="col-md-4 col-lg-2">
                <label for="location" class="form-label">Location</label>
                <select id="location" name="location" class="form-select">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" @selected(request('location') === $loc)>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Min Price --}}
            <div class="col-md-3 col-lg-2">
                <label for="min_price" class="form-label">Min Price (EGP)</label>
                <input type="number" id="min_price" name="min_price" class="form-control" value="{{ request('min_price') }}" placeholder="e.g., 500000" min="0">
            </div>

            {{-- Max Price --}}
            <div class="col-md-3 col-lg-2">
                <label for="max_price" class="form-label">Max Price (EGP)</label>
                <input type="number" id="max_price" name="max_price" class="form-control" value="{{ request('max_price') }}" placeholder="e.g., 2000000" min="0">
            </div>

            {{-- Sort --}}
            <div class="col-md-3 col-lg-2">
                <label for="sort_by" class="form-label">Sort By</label>
                <select id="sort_by" name="sort_by" class="form-select">
                    <option value="id DESC" @selected(request('sort_by') === 'id DESC')>Latest (Default)</option>
                    <option value="price ASC" @selected(request('sort_by') === 'price ASC')>Price: Low to High</option>
                    <option value="price DESC" @selected(request('sort_by') === 'price DESC')>Price: High to Low</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div class="col-md-3 col-lg-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
            <div class="col-md-3 col-lg-1 d-flex align-items-end">
                <a href="{{ route('properties.index') }}" class="btn btn-secondary w-100">Reset</a>
            </div>
        </form>
    </div>

    {{-- ================= PROPERTY CARDS ================= --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="properties-list">
        @forelse($properties as $property)
            <div class="col" data-id="{{ $property->id }}">
                <div class="card shadow-sm h-100">
                    @if($property->image)
                        <img src="{{ asset($property->image) }}" class="card-img-top object-fit-cover" alt="{{ $property->category }}">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">{{ $property->category }}</h5>
                            <span class="badge bg-secondary">ID: {{ $property->id }}</span>
                        </div>
                        <p class="card-text mb-1"><span class="text-info fw-bold">Status:</span> {{ $property->status }}</p>
                        <p class="card-text mb-1"><span class="text-info fw-bold">Location:</span> {{ $property->location }}</p>
                        <p class="card-text mb-1"><span class="text-info fw-bold">Type:</span> {{ ucfirst($property->transaction_type ?? 'N/A') }}</p>
                        <p class="card-text mb-3"><span class="text-success fw-bold">Price:</span> {{ number_format($property->price) }} EGP</p>
                        <div class="mt-auto">
                            <a href="{{ route('properties.show', ['property' => $property->id]) }}" class="btn btn-primary w-100">
                                <i class="fas fa-info-circle me-1"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center text-muted fs-5">No properties found matching your criteria.</p>
            </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>
