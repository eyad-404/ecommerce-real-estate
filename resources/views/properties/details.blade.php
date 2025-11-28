<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details - EL Kayan</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/properties-details.css') }}">
</head>
@php
    $reservation = $reservation ?? $property->reservation;
    $canReserve = $canReserve ?? false;
    $canCancel = $canCancel ?? false;
    $isPending = $isPending ?? false;
    $isSold = $isSold ?? ($property->status === 'sold');
@endphp

<body>

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
                <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('about-us') ? 'active' : '' }}" href="{{ route('about-us') }}">About Us</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold {{ Request::is('properties') ? 'active' : '' }}" href="{{ route('properties.index') }}">Properties</a></li>
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
                <li class="nav-item"><a class="btn btn-custom btn-sm fw-bold ms-2" href="{{ route('login.form') }}"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- ================= PAGE CONTENT ================= -->
<div class="container property-details-container">
    
    <a href="{{ route('properties.index') }}" class="back-button">
        <i class="bi bi-arrow-left"></i> Back to Listings
    </a>

    <h1 class="property-title">{{ $property->title ?? $property->category ?? 'Property Details' }}</h1>

    <div class="row g-4">
        {{-- Multiple Images --}}
        <div class="col-lg-7">
            <div class="property-images-card">
                <h2><i class="bi bi-images me-2"></i>Property Images</h2>
                <div class="property-image-grid">
                    @if($property->images && $property->images->count() > 0)
                        @foreach($property->images as $index => $image)
                            <div class="property-image-item">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                                   data-bs-image-url="{{ asset($image->image_path) }}"
                                   data-bs-image-index="{{ $index }}">
                                    <img src="{{ asset($image->image_path) }}"
                                         alt="Property Image">
                                </a>
                            </div>
                        @endforeach
                    @elseif($property->image)
                        <div class="property-image-item">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                               data-bs-image-url="{{ asset($property->image) }}"
                               data-bs-image-index="0">
                                <img src="{{ asset($property->image) }}"
                                     alt="Property Image">
                            </a>
                        </div>
                    @else
                        <div class="no-images">
                            <i class="bi bi-image fs-1 d-block mb-3"></i>
                            <p>No images available for this property.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Payment / Info --}}
        <div class="col-lg-5">
            <div class="payment-card">
                <div class="payment-card-header">
                    <h2><i class="bi bi-credit-card me-2"></i>Payment Options</h2>
                </div>
                <div class="payment-card-body">
                    <div class="payment-info">
                        @if($property->installment_years > 0)
                            <p class="{{ $canReserve ? 'text-success' : ($isPending ? 'text-warning' : ($isSold ? 'text-danger' : 'text-muted')) }}">
                                <strong>Installment Allowed:</strong>
                                @if($canReserve)
                                    Yes
                                @elseif($isPending)
                                    Pending Reservation
                                @elseif($canCancel)
                                    Reserved by you
                                @else
                                    Unavailable
                                @endif
                            </p>
                            <p><strong>Period:</strong> {{ $property->installment_years }} Years</p>
                        @else
                            <p class="text-danger"><strong>Payment Type:</strong> Cash payment only</p>
                        @endif
                    </div>

                    @auth
                        @if($canReserve)
                            <form action="{{ route('properties.reserve', $property->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="reserve-btn">
                                    <i class="bi bi-check-circle me-2"></i>Reserve this Property
                                </button>
                            </form>
                        @elseif($canCancel)
                            <form action="{{ route('properties.cancelReservation', $property->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="reserve-btn cancel">
                                    <i class="bi bi-x-circle me-2"></i>Cancel Reservation
                                </button>
                            </form>
                        @elseif($isPending)
                            <button type="button" class="reserve-btn pending" disabled>
                                <i class="bi bi-hourglass-split me-2"></i>Reserved
                            </button>
                        @else
                            <button type="button" class="reserve-btn sold-out" disabled>
                                <i class="bi bi-x-circle me-2"></i>Sold Out
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login.form') }}" class="reserve-btn login-link">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login to reserve
                        </a>
                    @endauth

                    @if($isPending && !$canCancel && $reservation)
                        <p class="text-warning mt-3 mb-0 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            This property is currently reserved by another user.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Property Info Card --}}
            <div class="payment-card">
                <div class="payment-card-header" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.2) 0%, rgba(11, 94, 215, 0.2) 100%);">
                    <h2><i class="bi bi-info-circle me-2"></i>Property Information</h2>
                </div>
                <div class="payment-card-body">
                    @if($property->location)
                        <div class="payment-info">
                            <p><strong><i class="bi bi-geo-alt me-2"></i>Location:</strong> {{ $property->location }}</p>
                        </div>
                    @endif
                    @if($property->price)
                        <div class="payment-info">
                            <p><strong><i class="bi bi-currency-dollar me-2"></i>Price:</strong> {{ number_format($property->price) }} EGP</p>
                        </div>
                    @endif
                    @if($property->status)
                        <div class="payment-info">
                            <p><strong><i class="bi bi-tag me-2"></i>Status:</strong> 
                                <span class="badge bg-{{ $property->status === 'available' ? 'success' : ($property->status === 'sold' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </p>
                        </div>
                    @endif
                    @if($property->transaction_type)
                        <div class="payment-info">
                            <p><strong><i class="bi bi-arrow-left-right me-2"></i>Type:</strong> {{ ucfirst($property->transaction_type) }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    @if($property->description)
    <div class="description-card mt-4">
        <h2><i class="bi bi-file-text me-2"></i>Description</h2>
        <p>{!! nl2br(e($property->description)) !!}</p>
    </div>
    @endif

</div>

{{-- IMAGE VIEWER MODAL --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Property Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" class="img-fluid" id="modalImage">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn" id="prevImage">Previous</button>
                <button type="button" class="btn" id="nextImage">Next</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const prevImageBtn = document.getElementById('prevImage');
    const nextImageBtn = document.getElementById('nextImage');

    let currentImageIndex = 0;
    let allImageUrls = [];

    document.querySelectorAll('[data-bs-image-url]').forEach(link => {
        allImageUrls.push(link.getAttribute('data-bs-image-url'));
    });

    imageModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const imageUrl = button.getAttribute('data-bs-image-url');
        const index = parseInt(button.getAttribute('data-bs-image-index'));

        modalImage.src = imageUrl;
        currentImageIndex = index;

        prevImageBtn.disabled = currentImageIndex === 0;
        nextImageBtn.disabled = currentImageIndex === allImageUrls.length - 1;
    });

    prevImageBtn.addEventListener('click', function() {
        if (currentImageIndex > 0) {
            currentImageIndex--;
            modalImage.src = allImageUrls[currentImageIndex];
            prevImageBtn.disabled = currentImageIndex === 0;
            nextImageBtn.disabled = false;
        }
    });

    nextImageBtn.addEventListener('click', function() {
        if (currentImageIndex < allImageUrls.length - 1) {
            currentImageIndex++;
            modalImage.src = allImageUrls[currentImageIndex];
            nextImageBtn.disabled = currentImageIndex === allImageUrls.length - 1;
            prevImageBtn.disabled = false;
        }
    });
});
</script>

</body>
</html>
