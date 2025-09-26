<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guided Tours')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preload" href="/images/unibslogo_micro.svg" as="image">

    <style>
        #toast-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 1055;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            pointer-events: none;
        }
        .toast-notification {
            background-color: var(--bs-dark, #212529);
            color: var(--bs-light, #f8f9fa);
            padding: 0.75rem 1.25rem;
            margin-bottom: 0.75rem;
            border-radius: var(--bs-border-radius, 0.25rem);
            box-shadow: var(--bs-box-shadow, 0 .5rem 1rem rgba(0, 0, 0, .15));
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s, visibility 0.5s, transform 0.5s;
            transform: translateX(100%);
            min-width: 250px;
            max-width: 400px;
            pointer-events: auto;
        }
        .toast-notification.show {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }
        .toast-notification.success {
            background-color: var(--bs-success, #198754);
            color: #fff;
        }
        .toast-notification.error {
            background-color: var(--bs-danger, #dc3545);
            color: #fff;
        }
        .toast-notification.warning {
            background-color: var(--bs-warning, #ffc107);
            color: #000;
        }
        .toast-notification.info {
            background-color: var(--bs-info, #0dcaf0);
            color: #000;
        }

        /* Logo esterno SVG, filtro per invertire colore */
        .unibs-logo-img {
            display: inline-block;
            width: 16px;
            height: auto;
            vertical-align: middle;
            filter: invert(1);
            transition: filter 0.2s;
        }
        .logo-btn:hover .unibs-logo-img {
            filter: invert(0);
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">Guided Tours Org</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    @auth
                        @if (Auth::user()->hasRole('fruitore'))
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('user.dashboard') ? 'active' : '' }}"
                                   href="{{ route('user.dashboard') }}">My Bookings</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('user.visits.past') ? 'active' : '' }}"
                                   href="{{ route('user.visits.past') }}">My Past Visits</a>
                            </li>
                        @endif
                        @if (Auth::user()->hasRole('configurator'))
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('admin.configurator') ? 'active' : '' }}"
                                   href="{{ route('admin.configurator') }}">Admin Panel</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('admin.visit-planning.index') ? 'active' : '' }}"
                                   href="{{ route('admin.visit-planning.index') }}">Visit Planning</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('admin.visits.past') ? 'active' : '' }}"
                                   href="{{ route('admin.visits.past') }}">Past Visits</a>
                            </li>
                        @elseif (Auth::user()->hasRole('volunteer'))
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('volunteer.availability.form') ? 'active' : '' }}"
                                   href="{{ route('volunteer.availability.form') }}">My Availability</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('volunteer.visits.past') ? 'active' : '' }}"
                                   href="{{ route('volunteer.visits.past') }}">Assigned Visits</a>
                            </li>
                        @endif

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->username }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item {{ Route::is('profile') ? 'active' : '' }}"
                                       href="{{ route('profile') }}">Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ Route::is('change-password.form') ? 'active' : '' }}"
                                       href="{{ route('change-password.form') }}">Change Password</a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('register') ? 'active' : '' }}" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 flex-grow-1">
        @yield('content')
    </main>

    <div id="toast-container"></div>

    <footer class="bg-dark text-white mt-auto py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <p>Sito di visite guidate.</p>
                </div>

                <div class="col-md-2">
                    <h6 class="text-uppercase mb-3 font-weight-bold">Structure</h6>
                    <ul class="list-unstyled">
                        <li><a href="https://laravel.com/" class="text-white">Laravel</a></li>
                        <li><a href="https://getbootstrap.com/" class="text-white">Bootstrap</a></li>
                        <li><a href="https://vite.dev/" class="text-white">Vite</a></li>
                    </ul>
                </div>

                <div class="col-md-2">
                    <h6 class="text-uppercase mb-3 font-weight-bold">Useful Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('careers') }}" class="text-white">Careers</a></li>
                        <li><a href="{{ route('terms') }}" class="text-white">Terms and Condition</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h6 class="text-uppercase mb-3 font-weight-bold">Contact</h6>
                    <p><i class="bi bi-house-door me-2"></i>Via Branze 38, Brescia BS 25123, IT</p>
                    <p><a href="mailto:d.barbetti@unibs.studenti.it">d.barbetti@unibs.studenti.it</a></p>
                    <p><a href="mailto:g.felappi004@unibs.studenti.it">g.felappi004@unibs.studenti.it</a></p>
                    <p><a href="mailto:m.cesari001@unibs.studenti.it">m.cesari001@unibs.studenti.it</a></p>
                </div>

                <div class="col-md-2">
                    <h6 class="text-uppercase mb-3 font-weight-bold">Follow Us</h6>
                    <a href="https://www.unibs.it/it" class="btn btn-outline-light btn-floating m-1 logo-btn" role="button">
                        <img src="/images/unibslogo_micro.svg" alt="UniBS" class="unibs-logo-img">
                    </a>
                    <a href="https://x.com/unibs_official/" class="btn btn-outline-light btn-floating m-1" role="button">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="https://www.instagram.com/unibs.official/" class="btn btn-outline-light btn-floating m-1" role="button">
                        <i class="bi bi-instagram"></i>
                    </a>
                </div>
            </div>

            <hr class="bg-secondary my-4">

            <div class="row">
                <div class="col text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} Guided Tours Org. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastContainer = document.getElementById('toast-container');

            window.showToast = function(text, type = 'info', duration = 5000) {
                if (!text || !toastContainer) {
                    console.warn('Toast container not found or no text provided.');
                    return;
                }

                const toast = document.createElement('div');
                toast.className = `toast-notification ${type}`;
                toast.textContent = text;
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');

                toastContainer.appendChild(toast);

                requestAnimationFrame(() => {
                    void toast.offsetWidth;
                    requestAnimationFrame(() => {
                        toast.classList.add('show');
                    });
                });

                const hideTimeout = setTimeout(() => {
                    toast.classList.remove('show');
                    toast.addEventListener('transitionend', () => {
                        if (toast.parentNode === toastContainer) {
                            toastContainer.removeChild(toast);
                        }
                    }, { once: true });

                    setTimeout(() => {
                        if (toast.parentNode === toastContainer) {
                            console.warn('Toast fallback removal triggered for:', text);
                            toastContainer.removeChild(toast);
                        }
                    }, 600);

                }, duration);
            }

            @if (session('status'))
                window.showToast("{{ session('status') }}", 'success');
            @endif
            @if (session('success'))
                window.showToast("{{ session('success') }}", 'success');
            @endif
            @if (session('error'))
                window.showToast("{{ session('error') }}", 'error');
            @endif
            @if (session('warning'))
                window.showToast("{{ session('warning') }}", 'warning');
            @endif
            @if (session('info'))
                window.showToast("{{ session('info') }}", 'info');
            @endif

            @if ($errors->any())
                window.showToast("{{ $errors->first() }}", 'error');
            @endif

        });
    </script>

    @stack('scripts')
</body>
</html>
