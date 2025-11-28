<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - EL Kayan</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="d-flex flex-column min-vh-100">

<!-- ================= NAVBAR ================= -->
<nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 text-black" href="{{ url('/') }}">
            EL Kayan
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        Home
                    </a>
                </li>

                <!-- About Us -->
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('about-us') ? 'active' : '' }}" href="{{ route('about-us') }}">
                        About Us
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('properties') ? 'active' : '' }}" href="{{ route('properties.index') }}">Properties</a>
                </li>
                <li class="nav-item">
                    @auth
                        @if(auth()->user()->role === ['admin','seller'])
                    <a class="nav-link fw-semibold {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        Analytics
                    </a>
                        @endif
                    @endauth

                </li>

                @auth
                <!-- Settings Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold d-flex align-items-center
                        {{ Request::is('users-management') || Request::is('property-management') ? 'active' : '' }}"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        Settings
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if(auth()->user()->role === 'admin')
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('users-management') }}">
                                    Users Management
                                </a>
                            </li>
                        @endif
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('property-management') }}">
                                Property Management
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img
                            src="{{ Auth::user()->profile_image_url }}"
                            alt="{{ Auth::user()->name }}"
                            class="rounded-circle profile-img me-2"
                        >
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile') }}">
                                Profile
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-custom btn-sm fw-bold ms-2" href="{{ route('login.form') }}">
                            Login
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- ================= MAIN CONTENT ================= -->
<div class="container-fluid" style="margin-top: 90px;">
  @if(auth()->user()->role === 'admin')
    <div class="section-title mt-4">Key Metrics</div>
    <section class="metrics mb-4">
      <div class="metric"><div class="label">Total Listings</div><div class="value" id="clientListings">{{ $totalListings }}</div></div>
      <div class="metric"><div class="label">Total Reservations</div><div class="value" id="clientReservations">{{ $totalReservations }}</div></div>
      <div class="metric"><div class="label">Website Visitors</div><div class="value" id="clientVisitors">{{ $totalVisitors }}</div></div>
    </section>
  @else
    <div class="section-title mt-4">Your Dashboard</div>
    <section class="metrics mb-4">
      <div class="metric"><div class="label">Your Listings</div><div class="value" id="clientListings">{{ $listings }}</div></div>
      <div class="metric"><div class="label">Your Reservations</div><div class="value" id="clientReservations">{{ $reservations }}</div></div>
      <div class="metric"><div class="label">Website Visitors</div><div class="value" id="clientVisitors">{{ $visitors ?? 0 }}</div></div>
    </section>
  @endif

  @if(auth()->user()->role === 'admin')
    <div class="section-title">Clients</div>
    <div class="clients-list mb-4">
      <select
        id="clientSelect"
        class="form-select w-25"
        data-endpoint="{{ route('dashboard.clientData') }}"
      >
        <option value="">All Clients</option>
        @foreach($clients as $client)
          <option value="{{ $client->id }}">{{ $client->name }}</option>
        @endforeach
      </select>
    </div>
  @endif

  <section class="charts-grid mb-4">
    <div class="chart">
      <h3 class="chart-title">Sales Summary</h3>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0" id="salesTable">
          <thead>
            <tr>
              <th scope="col">Month</th>
              <th scope="col">Listings</th>
              <th scope="col">Reservations</th>
            </tr>
          </thead>
          <tbody id="salesTableBody">
            @foreach($salesData as $point)
              <tr>
                <td>{{ $point['label'] ?? '—' }}</td>
                <td>{{ $point['listings'] ?? 0 }}</td>
                <td>{{ $point['reservations'] ?? 0 }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <p id="salesTableEmpty" class="text-muted small {{ count($salesData) ? 'd-none' : '' }}">No sales data available.</p>
    </div>
    <div class="chart">
      <h3 class="chart-title">Property Types</h3>
      <canvas class="canvas" id="pieChart"></canvas>
      @if(auth()->user()->role === 'admin')
        <div class="actions">
          <button class="btn" id="randomizeBtn">Randomize Data</button>
        </div>
      @endif
    </div>
  </section>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    window.dashboardConfig = {
        initialPie: @json($pieData),
        salesData: @json($salesData),
        clientDataUrl: "{{ route('dashboard.clientData') }}"
    };
</script>
<script src="{{ asset('js/dashboard.js') }}"></script>

</body>
</html>
