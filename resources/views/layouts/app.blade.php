<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ 
          theme: localStorage.getItem('theme') || 'light',
          commandOpen: false,
          toggleTheme() {
              this.theme = this.theme === 'light' ? 'dark' : 'light';
              localStorage.setItem('theme', this.theme);
          }
      }" 
      :data-theme="theme"
      @keydown.window.ctrl.k.prevent="commandOpen = !commandOpen"
      @keydown.window.escape="commandOpen = false">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guided Tours') | City Heritage Tours</title>
    
    <!-- Theme Flash Prevention -->
    <script>
        const theme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);
    </script>
    
    <!-- Scripts & Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Preload Logo -->
    <link rel="preload" href="/images/unibslogo_micro.svg" as="image">
    
    @stack('styles')
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Disable overscroll bounce effect */
        html, body {
            overscroll-behavior: none;
            -webkit-overscroll-behavior: none;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Global Command Palette Modal -->
    @auth
        <x-command-palette />
    @endauth

    <!-- Header / Navbar -->
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg glass-effect" id="main-navbar">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                    <img :src="theme === 'light' ? '/images/unibslogo-black.png' : '/images/unibslogo-white.png'" alt="UniBS Logo" class="me-2" style="width: 55px; height: 55px;">
                    <span :class="theme === 'dark' ? 'text-white' : 'text-dark'" class="fs-5 fw-bold">Guided Tours</span>
                </a>
                
                <div class="d-flex align-items-center gap-2 ms-auto order-lg-last">
                    
                    <!-- Command Palette Trigger (Desktop) -->
                    @auth
                    <button @click="commandOpen = true" class="btn btn-sm btn-light border rounded-pill px-3 text-muted d-none d-lg-flex align-items-center me-2 shadow-sm">
                        <i class="bi bi-search me-2"></i> <span class="me-2">Search...</span> <kbd class="bg-body-secondary text-body border-0 small font-monospace">Ctrl K</kbd>
                    </button>
                    <!-- Command Palette Trigger (Mobile) -->
                    <button @click="commandOpen = true" class="btn btn-icon btn-sm btn-ghost rounded-circle text-muted d-lg-none me-2">
                        <i class="bi bi-search"></i>
                    </button>
                    @endauth

                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()" class="btn btn-icon btn-sm btn-ghost rounded-circle text-muted" title="Toggle Theme">
                        <i class="bi" :class="theme === 'light' ? 'bi-moon-stars-fill' : 'bi-sun-fill'"></i>
                    </button>

                    <button class="navbar-toggler border-0 ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon" style="filter: invert(0.5);"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center me-3">
                        <li class="nav-item">
                            <a :class="theme === 'dark' ? 'text-white' : ''" class="nav-link {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>
                        
                        @auth
                            <!-- Role Based Links -->
                            @if (Auth::user()->hasRole('Customer'))
                                <li class="nav-item"><a :class="theme === 'dark' ? 'text-white' : ''" class="nav-link {{ Route::is('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">Bookings</a></li>
                            @endif

                            @if (Auth::user()->hasRole('Admin'))
                                <li class="nav-item"><a :class="theme === 'dark' ? 'text-white' : ''" class="nav-link {{ Route::is('admin.configurator') ? 'active' : '' }}" href="{{ route('admin.configurator') }}">Admin</a></li>
                                <li class="nav-item"><a :class="theme === 'dark' ? 'text-white' : ''" class="nav-link {{ Route::is('admin.visit-planning.index') ? 'active' : '' }}" href="{{ route('admin.visit-planning.index') }}">Planning</a></li>
                            @endif

                            @if (Auth::user()->hasRole('Guide'))
                                <li class="nav-item"><a :class="theme === 'dark' ? 'text-white' : ''" class="nav-link {{ Route::is('volunteer.availability.form') ? 'active' : '' }}" href="{{ route('volunteer.availability.form') }}">Availability</a></li>
                            @endif
                            
                            <!-- User Dropdown -->
                            <li class="nav-item dropdown ms-lg-3">
                                <a :class="theme === 'dark' ? 'text-dark' : ''" class="nav-link dropdown-toggle btn btn-light px-3 rounded-pill" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                                <a :class="theme === 'dark' ? 'text-white' : ''" class="nav-link" href="{{ route('login') }}">Login</a>
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
    <main class="flex-grow-1 pt-0 pb-4">
        @yield('content')
    </main>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" class="scroll-to-top-btn" title="Back to top">
        <i class="bi bi-chevron-up"></i>
    </button>

    <!-- Footer -->
    <footer :class="theme === 'light' ? 'bg-dark text-white' : 'bg-light text-dark'" class="mt-auto py-5">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6">
                    <h5 :class="theme === 'light' ? 'text-white' : 'text-dark'" class="mb-3">Guided Tours</h5>
                    <p :class="theme === 'light' ? 'text-white-50' : 'text-secondary'" class="small">The official platform for organizing and managing guided tours and cultural events.</p>
                    <div class="d-flex gap-2 mt-4">
                        <a href="https://www.unibs.it/it" class="btn btn-outline-light btn-sm btn-floating rounded-circle"><img :src="theme === 'light' ? '/images/unibslogo-black.png' : '/images/unibslogo-white.png'" style="width: 16px; height: 16px;"></a>
                        <a href="https://x.com/unibs_official/" class="btn btn-outline-light btn-sm btn-floating rounded-circle"><i class="bi bi-twitter"></i></a>
                        <a href="https://www.instagram.com/unibs.official/" class="btn btn-outline-light btn-sm btn-floating rounded-circle"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 :class="theme === 'light' ? 'text-white' : 'text-dark'" class="text-uppercase mb-3 fw-bold">Platform</h6>
                    <ul class="list-unstyled">
                        <li><a :class="theme === 'light' ? 'text-white' : 'text-dark'" class="small" style="text-decoration: none;" href="{{ route('about') }}">About Us</a></li>
                        <li><a :class="theme === 'light' ? 'text-white' : 'text-dark'" class="small" style="text-decoration: none;" href="{{ route('careers') }}">Careers</a></li>
                        <li><a :class="theme === 'light' ? 'text-white' : 'text-dark'" class="small" style="text-decoration: none;" href="{{ route('terms') }}">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h6 :class="theme === 'light' ? 'text-white' : 'text-dark'" class="text-uppercase mb-3 fw-bold">Contact</h6>
                    <ul class="list-unstyled" :class="theme === 'light' ? 'text-white-50' : 'text-secondary'" class="small">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> Via Branze 38, Brescia</li>
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> info@unibs.it</li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 text-lg-end">
                    <small :class="theme === 'light' ? 'text-white-50' : 'text-secondary'">&copy; {{ date('Y') }} Guided Tours Org.</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Legacy Validation Scripts support -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastContainer = document.getElementById('toast-container');
            // Toast functionality...
             window.showToast = function(text, type = 'info', duration = 5000) {
                if (!text || !toastContainer) return;
                const toast = document.createElement('div');
                toast.className = `toast-notification ${type}`;
                let icon = 'bi-info-circle';
                if(type === 'success') icon = 'bi-check-circle';
                if(type === 'error') icon = 'bi-exclamation-circle';
                if(type === 'warning') icon = 'bi-exclamation-triangle';
                toast.innerHTML = `<i class="bi ${icon} me-2 fs-5"></i> <span>${text}</span>`;
                toastContainer.appendChild(toast);
                requestAnimationFrame(() => toast.classList.add('show'));
                setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 600); }, duration);
            }

            @if (session('status')) window.showToast("{{ session('status') }}", 'success'); @endif
            @if (session('success')) window.showToast("{{ session('success') }}", 'success'); @endif
            @if (session('error')) window.showToast("{{ session('error') }}", 'error'); @endif
            @if ($errors->any()) window.showToast("{{ $errors->first() }}", 'error'); @endif
        });
    </script>
    
        <style>
        /* Add a solid background and shadow to the navbar when scrolled */
        .navbar {
            box-shadow: none !important;
            border: none !important;
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        /* Light theme scrolled state */
        html[data-theme="light"] .navbar.scrolled {
            background-color: #fff !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        }

        /* Dark theme scrolled state */
        html[data-theme="dark"] .navbar.scrolled {
            background-color: #2a2a2a !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4) !important;
        }

        /* User profile dropdown button in dark theme */
        html[data-theme="dark"] .navbar a.btn-light {
            background-color: #333 !important;
            color: #e0e0e0 !important;
            border-color: #444 !important;
        }

        html[data-theme="dark"] .navbar a.btn-light:hover {
            background-color: #444 !important;
            color: #fff !important;
        }

        /* Dropdown menu styling for dark theme */
        html[data-theme="dark"] .dropdown-menu {
            background-color: #2a2a2a !important;
            border-color: #444 !important;
        }

        html[data-theme="dark"] .dropdown-item {
            color: #e0e0e0 !important;
        }

        html[data-theme="dark"] .dropdown-item:hover,
        html[data-theme="dark"] .dropdown-item:focus {
            background-color: #333 !important;
            color: #fff !important;
        }

        html[data-theme="dark"] .dropdown-divider {
            border-color: #444 !important;
        }

        /* Keep logout button red in dark theme */
        html[data-theme="dark"] .dropdown-item.text-danger {
            color: #dc3545 !important;
        }

        html[data-theme="dark"] .dropdown-item.text-danger:hover,
        html[data-theme="dark"] .dropdown-item.text-danger:focus {
            color: #ff6b6b !important;
        }

        /* Toast Notifications */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast-notification {
            background: white;
            padding: 15px 20px;
            border-radius: 12px; /* Rounded-4ish */
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            max-width: 350px;
            font-size: 0.95rem;
            color: #333;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .toast-notification.show {
            transform: translateX(0);
        }
        .toast-notification.success { border-left: 5px solid #198754; }
        .toast-notification.error { border-left: 5px solid #dc3545; }
        .toast-notification.warning { border-left: 5px solid #ffc107; }
        .toast-notification.info { border-left: 5px solid #0d6efd; }
        .toast-notification i { font-size: 1.25rem; }
        .toast-notification.success i { color: #198754; }
        .toast-notification.error i { color: #dc3545; }
        .toast-notification.warning i { color: #ffc107; }
        .toast-notification.info i { color: #0d6efd; }

        /* Scroll to Top Button */
        .scroll-to-top-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 999;
            transition: all 0.3s ease-in-out;
        }

        .scroll-to-top-btn:hover {
            background-color: #0b5ed7;
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        .scroll-to-top-btn.show {
            display: flex;
        }

        /* Dark theme scroll button */
        html[data-theme="dark"] .scroll-to-top-btn {
            background-color: #444;
            color: #e0e0e0;
        }

        html[data-theme="dark"] .scroll-to-top-btn:hover {
            background-color: #555;
            color: #fff;
        }

        /* Load More Button */
        .load-more-btn {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }

        .load-more-btn:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4);
            color: white;
            text-decoration: none;
            border-color: rgba(255, 255, 255, 0.3);
        }

        .load-more-btn:active {
            transform: translateY(0);
        }

        /* Dark theme load more button */
        html[data-theme="dark"] .load-more-btn {
            background: linear-gradient(135deg, #444 0%, #333 100%);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        html[data-theme="dark"] .load-more-btn:hover {
            background: linear-gradient(135deg, #555 0%, #444 100%);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 255, 255, 0.25);
        }

        /* Load More Button Loading State */
        .load-more-btn:disabled,
        .load-more-btn[data-loading="true"] {
            opacity: 0.8;
            cursor: not-allowed;
            pointer-events: none;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
            color: currentColor;
        }

        .spinner-border {
            display: inline-block;
            vertical-align: text-bottom;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        /* Smooth fade-in animation for new tours */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tour-card {
            animation-duration: 0.6s;
            animation-timing-function: ease-out;
            animation-fill-mode: both;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.getElementById('main-navbar');
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');
            const loadMoreLink = document.querySelector('.load-more-link');
            
            if (navbar) {
                // Function to handle scroll event
                const handleScroll = () => {
                    if (window.scrollY > 10) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                    
                    // Show/hide scroll to top button
                    if (scrollToTopBtn) {
                        if (window.scrollY > 300) {
                            scrollToTopBtn.classList.add('show');
                        } else {
                            scrollToTopBtn.classList.remove('show');
                        }
                    }
                };
                
                // Add scroll event listener
                window.addEventListener('scroll', handleScroll);

                // Initial check in case the page is loaded already scrolled
                handleScroll();
            }
            
            // Scroll to top button functionality
            if (scrollToTopBtn) {
                scrollToTopBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }

            // Load More button loading state
            if (loadMoreLink) {
                loadMoreLink.addEventListener('click', function(e) {
                    if (loadMoreLink.dataset.loading === 'true') {
                        e.preventDefault();
                        return;
                    }
                    
                    loadMoreLink.dataset.loading = 'true';
                    loadMoreLink.querySelector('.btn-content').style.display = 'none';
                    loadMoreLink.querySelector('.btn-loading').style.display = 'inline';
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
