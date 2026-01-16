<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guided Tours') | City Heritage Tours</title>
    
    <!-- Scripts & Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Preload Logo -->
    <link rel="preload" href="/images/unibslogo_micro.svg" as="image">
    
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Header / Navbar -->
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg glass-effect">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                    <img src="/images/unibslogo_micro.svg" alt="UniBS Logo" class="me-2">
                    <span>Guided Tours</span>
                </a>
                
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon" style="filter: invert(0.5);"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>
                        
                        @auth
                            <!-- Role Based Links -->
                            @if (Auth::user()->hasRole('fruitore'))
                                <li class="nav-item"><a class="nav-link {{ Route::is('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">Bookings</a></li>
                                <li class="nav-item"><a class="nav-link {{ Route::is('user.visits.past') ? 'active' : '' }}" href="{{ route('user.visits.past') }}">History</a></li>
                            @endif

                            @if (Auth::user()->hasRole('configurator'))
                                <li class="nav-item"><a class="nav-link {{ Route::is('admin.configurator') ? 'active' : '' }}" href="{{ route('admin.configurator') }}">Admin</a></li>
                                <li class="nav-item"><a class="nav-link {{ Route::is('admin.visit-planning.index') ? 'active' : '' }}" href="{{ route('admin.visit-planning.index') }}">Planning</a></li>
                            @endif

                            @if (Auth::user()->hasRole('volunteer'))
                                <li class="nav-item"><a class="nav-link {{ Route::is('volunteer.availability.form') ? 'active' : '' }}" href="{{ route('volunteer.availability.form') }}">Availability</a></li>
                                <li class="nav-item"><a class="nav-link {{ Route::is('volunteer.visits.past') ? 'active' : '' }}" href="{{ route('volunteer.visits.past') }}">My Visits</a></li>
                            @endif
                            
                            <!-- User Dropdown -->
                            <li class="nav-item dropdown ms-lg-3">
                                <a class="nav-link dropdown-toggle btn btn-light px-3 rounded-pill" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->username }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item py-2" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i> Profile</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('change-password.form') }}"><i class="bi bi-key me-2"></i> Password</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <!-- Guest Links -->
                            <li class="nav-item ms-lg-2">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                            <li class="nav-item ms-lg-2">
                                <a class="btn btn-primary rounded-pill px-4" href="{{ route('register') }}">Register</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow-1 py-4">
        @yield('content')
    </main>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Footer -->
    <footer class="mt-auto py-5">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white mb-3">Guided Tours</h5>
                    <p class="small text-white-50"> The official platform for organizing and managing guided tours and cultural events.</p>
                    <div class="d-flex gap-2 mt-4">
                        <a href="https://www.unibs.it/it" class="btn btn-outline-light btn-sm btn-floating rounded-circle"><img src="/images/unibslogo_micro.svg" style="width: 16px; height: 16px; filter: invert(1);"></a>
                        <a href="https://x.com/unibs_official/" class="btn btn-outline-light btn-sm btn-floating rounded-circle"><i class="bi bi-twitter"></i></a>
                        <a href="https://www.instagram.com/unibs.official/" class="btn btn-outline-light btn-sm btn-floating rounded-circle"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="text-uppercase mb-3 font-weight-bold text-primary">Platform</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white small">About Us</a></li>
                        <li><a href="{{ route('careers') }}" class="text-white small">Careers</a></li>
                        <li><a href="{{ route('terms') }}" class="text-white small">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-uppercase mb-3 font-weight-bold text-primary">Contact</h6>
                    <ul class="list-unstyled text-white-50 small">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> Via Branze 38, Brescia</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> info@unibs.it</li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 text-lg-end">
                    <small class="text-white-50">&copy; {{ date('Y') }} Guided Tours Org.<br>All rights reserved.</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastContainer = document.getElementById('toast-container');

            window.showToast = function(text, type = 'info', duration = 5000) {
                if (!text || !toastContainer) return;

                const toast = document.createElement('div');
                toast.className = `toast-notification ${type}`;
                
                // Add icon based on type
                let icon = 'bi-info-circle';
                if(type === 'success') icon = 'bi-check-circle';
                if(type === 'error') icon = 'bi-exclamation-circle';
                if(type === 'warning') icon = 'bi-exclamation-triangle';
                
                toast.innerHTML = `<i class="bi ${icon} me-2 fs-5"></i> <span>${text}</span>`;
                toast.setAttribute('role', 'alert');

                toastContainer.appendChild(toast);

                requestAnimationFrame(() => {
                    toast.classList.add('show');
                });

                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 600);
                }, duration);
            }

            @if (session('status')) window.showToast("{{ session('status') }}", 'success'); @endif
            @if (session('success')) window.showToast("{{ session('success') }}", 'success'); @endif
            @if (session('error')) window.showToast("{{ session('error') }}", 'error'); @endif
            @if (session('warning')) window.showToast("{{ session('warning') }}", 'warning'); @endif
            @if (session('info')) window.showToast("{{ session('info') }}", 'info'); @endif
            @if ($errors->any()) window.showToast("{{ $errors->first() }}", 'error'); @endif
        });
    </script>
    
    @stack('scripts')
</body>
</html>
