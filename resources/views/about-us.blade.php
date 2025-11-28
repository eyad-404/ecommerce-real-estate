<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - EL Kayan</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
</head>
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
                <li class="nav-item"><a class="btn btn-custom btn-sm fw-bold ms-2" href="{{ route('login.form') }}"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- ================= PAGE CONTENT ================= -->
<div class="container my-5 pt-5">

    <!-- Page Header -->
    <div class="text-center mb-5 fade-up">
        <h1 class="display-4 fw-bold text-light">About Us</h1>
        <p class="lead text-secondary-light">Learn more about EL Kayan and our mission in the real estate market.</p>
    </div>

    <!-- About Section -->
    <div class="row align-items-center mb-5 fade-up">
        <div class="col-md-6 mb-4 mb-md-0">
            <img src="{{ url('images/main1.jpg') }}" class="img-fluid rounded shadow-lg about-image glow-hover" alt="EL Kayan">
        </div>
        <div class="col-md-6 text-light">
            <h2 class="fw-bold mb-3">Who We Are</h2>
            <p>EL Kayan is a modern real estate platform dedicated to connecting buyers, sellers, and renters with their ideal properties...</p>
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="row text-center mb-5">
        <div class="col-md-6 mb-4 fade-up">
            <div class="p-4 bg-dark rounded shadow-sm hover-card glow-hover">
                <h3 class="fw-bold mb-3 text-primary">Our Mission</h3>
                <p class="text-light">To simplify real estate transactions...</p>
            </div>
        </div>
        <div class="col-md-6 mb-4 fade-up">
            <div class="p-4 bg-dark rounded shadow-sm hover-card glow-hover">
                <h3 class="fw-bold mb-3 text-primary">Our Vision</h3>
                <p class="text-light">To be the leading real estate platform in the region...</p>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="text-center mb-5 fade-up">
        <h2 class="fw-bold mb-4 text-light">Meet Our Team</h2>
        <div class="row justify-content-center">
            @php
                $team = [
                    ['img'=>'Eyad1.jpg','name'=>'Eyad Ashraf','role'=>'Back-end Developer'],
                    ['img'=>'pola.jpg','name'=>'Pola Raouf','role'=>'Back-end Developer'],
                    ['img'=>'Ahmed.jpg','name'=>'Ahmed Tamer','role'=>'Front-end Developer'],
                    ['img'=>'bashmo.jpg','name'=>'Abdelrahman','role'=>'Front-end Developer'],
                    ['img'=>'nour.jpg','name'=>'Nour Mohey','role'=>'Front-end Developer'] // new member
                ];
            @endphp

            @foreach($team as $member)
            <div class="col-md-3 mb-4 fade-up">
                <div class="card shadow-sm hover-card glow-hover">
                    <img src="{{ url('images/about-us/'.$member['img']) }}" class="card-img-top team-img glow-hover" alt="{{ $member['name'] }}">
                    <div class="card-body text-center rounded-name">
                        <h5 class="card-title fw-bold text-black">{{ $member['name'] }}</h5>
                        <p class="card-text text-secondary-light">{{ $member['role'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- New Member Spotlight -->
    <div class="row align-items-center mb-5 fade-up new-member-card">
        <div class="col-md-5 mb-4 mb-md-0">
            <img src="{{ url('images/about-us/nour.jpg') }}" class="img-fluid rounded shadow-lg glow-hover" alt="New Team Member">
        </div>
        <div class="col-md-7 text-light">
            <h2 class="fw-bold mb-3">Welcoming Our Newest Member</h2>
            <p>
                Say hello to <strong>Nour Mohey</strong>, our newest Front-end Developer.
                Nour specializes in crafting smooth user experiences, ensuring every interaction with EL Kayan feels modern, fast,
                and intuitive.
            </p>
            <p>
                She joins our mission to keep the platform accessible and visually engaging—across the analytics dashboard,
                listings, and beyond.
            </p>
        </div>
    </div>

</div>

<!-- ========================= SCRIPTS ========================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const faders = document.querySelectorAll('.fade-up');
    const appearOptions = { threshold: 0.2, rootMargin: "0px 0px -50px 0px" };
    const appearOnScroll = new IntersectionObserver(function(entries, observer){
        entries.forEach(entry => {
            if(!entry.isIntersecting) return;
            entry.target.classList.add('appear');
            observer.unobserve(entry.target);
        });
    }, appearOptions);

    faders.forEach(fader => appearOnScroll.observe(fader));
});
</script>

</body>
</html>
