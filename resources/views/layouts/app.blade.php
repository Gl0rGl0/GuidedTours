<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ 
          theme: document.documentElement.getAttribute('data-theme') || 'light',
          commandOpen: false,
          toggleTheme(event) {
              const isDark = this.theme === 'light';
              const applyTheme = () => {
                  this.theme = isDark ? 'dark' : 'light';
                  localStorage.setItem('theme', this.theme);
                  document.documentElement.setAttribute('data-theme', this.theme);
              };

              if (!document.startViewTransition || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                  applyTheme();
                  return;
              }

              const x = event?.clientX ?? window.innerWidth / 2;
              const y = event?.clientY ?? window.innerHeight / 2;
              const endRadius = Math.hypot(
                  Math.max(x, innerWidth - x),
                  Math.max(y, innerHeight - y)
              );

              document.documentElement.classList.add('theme-transitioning');
              const transition = document.startViewTransition(applyTheme);

                transition.ready.then(() => {
                    // 1. Cambia 15px con 0px!
                    const clipPath = [
                        `circle(1px at ${x}px ${y}px)`,
                        `circle(${endRadius}px at ${x}px ${y}px)`
                    ];
                    
                    // 2. Anziché usare 'direction', invertiamo brutalmente l'array per la chiusura.
                    // È molto più stabile per il rendering del browser.
                    const animationClip = isDark ? clipPath : [...clipPath].reverse();

                    document.documentElement.animate(
                        { clipPath: animationClip },
                        {
                            duration: 500,
                            easing: 'ease-in-out',
                            pseudoElement: isDark ? '::view-transition-new(root)' : '::view-transition-old(root)',
                            fill: 'forwards' // 3. FONDAMENTALE: impedisce il flash nell'ultimo millisecondo
                        }
                    );
                });

              transition.finished.then(() => {
                  document.documentElement.classList.remove('theme-transitioning');
              });
          }
      }" :data-theme="theme" @keydown.window.ctrl.k.prevent="commandOpen = !commandOpen"
    @keydown.window.escape="commandOpen = false">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guided Tours') | City Heritage Tours</title>

    <!-- Theme Flash Prevention -->
    <script>
        const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
    </script>

    <!-- Scripts & Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Preload Logo -->
    <link rel="preload" href="/images/unibslogo_micro.svg" as="image">

    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Disable overscroll bounce effect */
        html,
        body {
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
                    <img :src="theme === 'light' ? '/images/unibslogo-black.png' : '/images/unibslogo-white.png'"
                        alt="UniBS Logo" class="me-2" style="width: 55px; height: 55px;">
                    <span :class="theme === 'dark' ? 'text-white' : 'text-dark'" class="fs-5 fw-bold">Guided
                        Tours</span>
                </a>

                <div class="d-flex align-items-center gap-2 ms-auto order-lg-last">

                    <!-- Command Palette Trigger (Desktop) -->
                    @auth
                        <button @click="commandOpen = true"
                            class="btn btn-sm btn-light border rounded-pill px-3 text-muted d-none d-lg-flex align-items-center me-2 shadow-sm">
                            <i class="bi bi-search me-2"></i> <span class="me-2">Search...</span> <kbd
                                class="bg-body-secondary text-body border-0 small font-monospace">Ctrl K</kbd>
                        </button>
                        <!-- Command Palette Trigger (Mobile) -->
                        <button @click="commandOpen = true"
                            class="btn btn-icon btn-sm btn-ghost rounded-circle text-muted d-lg-none me-2">
                            <i class="bi bi-search"></i>
                        </button>
                    @endauth

                    <!-- Theme Toggle -->
                    <button @click="toggleTheme($event)" class="btn btn-icon btn-sm btn-ghost rounded-circle text-muted"
                        title="Toggle Theme">
                        <i class="bi" :class="theme === 'light' ? 'bi-moon-stars-fill' : 'bi-sun-fill'"></i>
                    </button>

                    <button class="navbar-toggler border-0 ms-2" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon" style="filter: invert(0.5);"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center me-3">
                        <li class="nav-item">
                            <a :class="theme === 'dark' ? 'text-white' : ''"
                                class="nav-link {{ Route::is('home') ? 'active' : '' }}"
                                href="{{ route('home') }}">Home</a>
                        </li>

                        @auth
                            <!-- Role Based Links -->
                            @role('Customer')
                                <li class="nav-item"><a :class="theme === 'dark' ? 'text-white' : ''"
                                        class="nav-link {{ Route::is('user.dashboard') ? 'active' : '' }}"
                                        href="{{ route('user.dashboard') }}">Bookings</a></li>
                            @endrole

                            @role('Admin')
                                <li class="nav-item"><a :class="theme === 'dark' ? 'text-white' : ''"
                                        class="nav-link {{ Route::is('admin.configurator') ? 'active' : '' }}"
                                        href="{{ route('admin.configurator') }}">Admin</a></li>
                                <li class="nav-item"><a :class="theme === 'dark' ? 'text-white' : ''"
                                        class="nav-link {{ Route::is('admin.visit-planning.index') ? 'active' : '' }}"
                                        href="{{ route('admin.visit-planning.index') }}">Planning</a></li>
                            @endrole

                            @role('Guide')
                                <li class="nav-item"><a :class="theme === 'dark' ? 'text-white' : ''"
                                        class="nav-link {{ Route::is('volunteer.availability.form') ? 'active' : '' }}"
                                        href="{{ route('volunteer.availability.form') }}">Availability</a></li>
                                <li class="nav-item"><a :class="theme === 'dark' ? 'text-white' : ''"
                                        class="nav-link {{ Route::is('volunteer.visits.past') ? 'active' : '' }}"
                                        href="{{ route('volunteer.visits.past') }}">My Visits</a></li>
                            @endrole

                            <!-- User Dropdown -->
                            <li class="nav-item dropdown ms-lg-3">
                                <a :class="theme === 'dark' ? 'text-dark' : ''"
                                    class="nav-link dropdown-toggle btn btn-light px-3 rounded-pill" href="#"
                                    id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->first_name }}
                                    {{ Auth::user()->last_name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0"
                                    aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item py-2" href="{{ route('profile') }}"><i
                                                class="bi bi-person me-2"></i> Profile</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('change-password.form') }}"><i
                                                class="bi bi-key me-2"></i> Password</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger py-2"><i
                                                    class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <!-- Guest Links -->
                            <li class="nav-item ms-lg-2">
                                <a :class="theme === 'dark' ? 'text-white' : ''" class="nav-link"
                                    href="{{ route('login') }}">Login</a>
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

    <!-- Main Content Area -->
    <main class="flex-grow-1" id="main-content">
        @hasSection('full-width-content')
            @yield('full-width-content')
        @else
            <div class="container py-5 my-4">
                @yield('content')
            </div>
        @endif
    </main>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Footer -->
    <footer class="theme-footer mt-auto py-5">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="mb-3">Guided Tours</h5>
                    <p class="small">The official
                        platform for organizing and managing guided tours and cultural events.</p>
                    <div class="d-flex gap-2 mt-4">
                        <a href="https://www.unibs.it/it"
                            class="btn btn-outline-light btn-sm btn-floating rounded-circle"><img
                                src="/images/unibslogo-white.png"
                                style="width: 16px; height: 16px;"></a>
                        <a href="https://x.com/unibs_official/"
                            class="btn btn-outline-light btn-sm btn-floating rounded-circle"><i
                                class="bi bi-twitter"></i></a>
                        <a href="https://www.instagram.com/unibs.official/"
                            class="btn btn-outline-light btn-sm btn-floating rounded-circle"><i
                                class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="text-uppercase mb-3 fw-bold">
                        Platform</h6>
                    <ul class="list-unstyled">
                        <li><a class="small"
                                style="text-decoration: none;" href="{{ route('about') }}">About Us</a></li>
                        <li><a class="small"
                                style="text-decoration: none;" href="{{ route('careers') }}">Careers</a></li>
                        <li><a class="small"
                                style="text-decoration: none;" href="{{ route('terms') }}">Terms of Service</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="text-uppercase mb-3 fw-bold">
                        Contact</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> Via Branze 38, Brescia</li>
                        <p><a href="mailto:d.barbetti@unibs.studenti.it">d.barbetti@unibs.studenti.it</a></p>
                        <p><a href="mailto:g.felappi004@unibs.studenti.it">g.felappi004@unibs.studenti.it</a></p>
                        <p><a href="mailto:m.cesari001@unibs.studenti.it">m.cesari001@unibs.studenti.it</a></p>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 text-lg-end">
                    <small>&copy; {{ date('Y') }} Guided
                        Tours Org.</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Legacy Validation Scripts support -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastContainer = document.getElementById('toast-container');
            // Toast functionality...
            window.showToast = function (text, type = 'info', duration = 5000) {
                if (!text || !toastContainer) return;
                const toast = document.createElement('div');
                toast.className = `toast-notification ${type}`;
                let icon = 'bi-info-circle';
                if (type === 'success') icon = 'bi-check-circle';
                if (type === 'error') icon = 'bi-exclamation-circle';
                if (type === 'warning') icon = 'bi-exclamation-triangle';
                toast.innerHTML = `
                    <i class="bi ${icon} me-2 fs-5"></i> 
                    <span style="flex-grow: 1; margin-right: 15px;">${text}</span>
                    `;
                toastContainer.appendChild(toast);

                toast.style.cursor='pointer';

                toast.addEventListener('click', () => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                });

                requestAnimationFrame(() => toast.classList.add('show'));

                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.classList.remove('show');
                        setTimeout(() => toast.remove(), 600);
                    }
                }, duration);
            }

            @if (session('status')) window.showToast("{{ session('status') }}", 'success'); @endif
            @if (session('success')) window.showToast("{{ session('success') }}", 'success'); @endif
            @if (session('error')) window.showToast("{{ session('error') }}", 'error'); @endif
            @if (session('error_message')) window.showToast("{{ session('error_message') }}", 'error'); @endif
            @if ($errors->any()) window.showToast("{{ $errors->first() }}", 'error'); @endif
        });
    </script>

    <style>
        /* Add a solid background and shadow to the navbar when scrolled */
        .navbar {
            box-shadow: none !important;
            border: none !important;
            transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.3s ease-in-out;
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
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navbar = document.getElementById('main-navbar');
            if (navbar) {
                // Function to handle scroll event
                const handleScroll = () => {
                    if (window.scrollY > 10) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                };

                // Add scroll event listener
                window.addEventListener('scroll', handleScroll);

                // Initial check in case the page is loaded already scrolled
                handleScroll();
            }
        });
    </script>

    @stack('scripts')
</body>

</html>