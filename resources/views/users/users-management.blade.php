<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - EL Kayan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/users-management.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@php($today = now()->toDateString())
@php($today = now()->toDateString())
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
                    <a class="nav-link fw-semibold {{ Request::is('about-us') ? 'active' : '' }}" href="{{ route('properties.index') }}">About us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('properties') ? 'active' : '' }}" href="{{ route('properties.index') }}">Properties</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                @if(auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link fw-semibold {{ Request::is('users-management') ? 'active' : '' }}" href="{{ route('users-management') }}">User Management</a>
                    </li>
                @endif
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

<div id="toast-container"></div>

<!-- ================= USERS MANAGEMENT CONTAINER ================= -->
<div class="users-management-container">

    <!-- LEFT PANEL: User Form -->
    <div class="user-info-panel">
        <h2 class="panel-title">Add User</h2>
        <form id="add-user-form" action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="field-list">
                <div class="field-label">Full Name</div>
                <div class="field-control"><input type="text" name="name" class="input-edit" placeholder="Full name" required></div>

                <div class="field-label">Email</div>
                <div class="field-control"><input type="email" name="email" class="input-edit" placeholder="example@gmail.com" required></div>

                <div class="field-label">Password</div>
                <div class="field-control"><input type="password" name="password" class="input-edit" placeholder="Enter password" required></div>

                <div class="field-label">Phone</div>
                <div class="field-control"><input type="tel" name="phone" class="input-edit" placeholder="+201234567890" required></div>

                <div class="field-label">Role</div>
                <div class="field-control">
                    <select name="role" class="input-edit" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="seller">Seller</option>
                        <option value="buyer">Buyer</option>
                    </select>
                </div>
            </div>
                <div class="field-label">Birth Date</div>
                <div class="field-control"><input type="date" name="birth_date" class="input-edit" required max="{{ $today }}"></div>

                <div class="field-label">Gender</div>
                <div class="field-control">
                    <select name="gender" class="input-edit" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="field-label">Location</div>
                <div class="field-control"><input type="text" name="location" class="input-edit" placeholder="Location" required></div>
                <div class="button-group mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add User</button>
            </div>
        </form>
    </div>

    <!-- RIGHT PANEL: Users List -->
    <div class="users-list-panel">
        <!-- Fixed Title + Search -->
        <div class="users-list-header">
            <h2 class="panel-title">Users List</h2>
            <div class="search-bar" data-route="{{ route('users.search') }}">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Search users by name, email, or phone...">
            </div>
        </div>

        <!-- Scrollable Table -->
        <div class="users-table-container">
            <table class="users-table table table-hover">
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr
                        data-id="{{ $user->id }}"
                        data-birth_date="{{ $user->birth_date }}"
                        data-gender="{{ $user->gender }}"
                        data-location="{{ $user->location }}"
                        data-role="{{ $user->role }}"
                    >
                        <td>{{ $user->id }}</td>
                        <td class="user-name">{{ $user->name }}</td>
                        <td class="user-email">{{ $user->email }}</td>
                        <td class="user-phone">{{ $user->phone }}</td>
                        <td class="user-role">{{ ucfirst($user->role) }}</td>
                        <td>
                            <button class="btn btn-secondary btn-sm edit-btn"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $user->id }}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- DELETE USER MODAL -->
<div id="delete-modal" class="modal-overlay" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <h5 class="modal-title">Confirm Deletion</h5>
            <button type="button" class="modal-close" id="delete-close">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this user?</p>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" id="delete-cancel">Cancel</button>
            <form id="delete-form" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div id="edit-modal" class="modal-overlay" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <h5 class="modal-title">Edit User</h5>
            <button type="button" class="modal-close" id="edit-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="edit-user-form">
                @csrf
                <input type="hidden" id="edit-user-id">
                <label>Full Name</label>
                <input type="text" id="edit-name" required>
                <label>Email</label>
                <input type="email" id="edit-email" required>
                <label>Password (leave blank to keep)</label>
                <input type="password" id="edit-password">
                <label>Phone</label>
                <input type="tel" id="edit-phone" required>
                <label>Role</label>
                <select id="edit-role" required>
                    <option value="admin">Admin</option>
                    <option value="seller">Seller</option>
                    <option value="buyer">Buyer</option>
                </select>
                <label>Birth Date</label>
                <input type="date" id="edit-birth_date" required max="{{ $today }}">

                <label>Gender</label>
                <select id="edit-gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>

                <label>Location</label>
                <input type="text" id="edit-location" required>
                <div class="modal-actions mt-3">
                    <button type="button" class="btn btn-secondary" id="edit-cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap & Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/users-management.js') }}"></script>
</body>
</html>
