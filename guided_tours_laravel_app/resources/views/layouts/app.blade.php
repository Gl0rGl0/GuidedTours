<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> {{-- Use Laravel's locale --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guided Tours')</title> {{-- Allow overriding title --}}
    {{-- Load compiled assets using Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Add CSRF Token for forms --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Add any other common head elements here --}}

    {{-- === START: Centralized Toast Styles === --}}
    <style>
        #toast-container {
            position: fixed;
            top: 1.5rem; /* Adjust spacing as needed */
            right: 1.5rem; /* Adjust spacing as needed */
            z-index: 1055; /* Ensure it's above Bootstrap modals (usually 1050) */
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            pointer-events: none; /* Allow clicks to pass through the container */
        }
        .toast-notification { /* Renamed class slightly for clarity */
            background-color: var(--bs-dark, #212529); /* Default info style using BS variable */
            color: var(--bs-light, #f8f9fa);
            padding: 0.75rem 1.25rem; /* Use BS padding variables if desired */
            margin-bottom: 0.75rem;
            border-radius: var(--bs-border-radius, 0.25rem); /* Use BS border-radius */
            box-shadow: var(--bs-box-shadow, 0 .5rem 1rem rgba(0, 0, 0, .15)); /* Use BS shadow */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s, visibility 0.5s, transform 0.5s;
            transform: translateX(100%); /* Start off-screen */
            min-width: 250px;
            max-width: 400px;
            pointer-events: auto; /* Enable pointer events for individual toasts */
        }
        .toast-notification.show {
            opacity: 1;
            visibility: visible;
            transform: translateX(0); /* Slide in */
        }
        .toast-notification.success {
            background-color: var(--bs-success, #198754);
             color: #fff; /* Ensure contrast */
        }
        .toast-notification.error {
            background-color: var(--bs-danger, #dc3545);
             color: #fff; /* Ensure contrast */
        }
        .toast-notification.warning {
            background-color: var(--bs-warning, #ffc107);
             color: #000; /* Better contrast for warning */
        }
         .toast-notification.info { /* Explicit style for info */
            background-color: var(--bs-info, #0dcaf0);
             color: #000; /* Better contrast for info */
        }
    </style>
    {{-- === END: Centralized Toast Styles === --}}

    @stack('styles') {{-- Placeholder for page-specific styles --}}
</head>
<body class="d-flex flex-column min-vh-100"> {{-- Ensure footer sticks to bottom --}}
    {{-- Bootstrap Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">Guided Tours Org</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto"> {{-- Align items to the right --}}
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    @auth {{-- Check if user is logged in --}}
                        {{-- Add link to Fruitore Dashboard if user is a fruitore --}}
                        @if (Auth::user()->hasRole('fruitore'))
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">My Bookings</a>
                            </li>
                        @endif
                        @if (Auth::user()->hasRole('configurator'))
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('admin.configurator') ? 'active' : '' }}" href="{{ route('admin.configurator') }}">Admin Panel</a>
                            </li>
                            {{-- Add link to Visit Planning for Configurators --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('admin.visit-planning.index') ? 'active' : '' }}" href="{{ route('admin.visit-planning.index') }}">Visit Planning</a>
                            </li>
                        @elseif (Auth::user()->hasRole('volunteer'))
                             <li class="nav-item">
                                <a class="nav-link {{ Route::is('volunteer.availability.form') ? 'active' : '' }}" href="{{ route('volunteer.availability.form') }}">My Availability</a>
                            </li>
                        @endif
                        {{-- User Dropdown --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->username }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item {{ Route::is('profile') ? 'active' : '' }}" href="{{ route('profile') }}">Profile</a></li>
                                <li><a class="dropdown-item {{ Route::is('change-password.form') ? 'active' : '' }}" href="{{ route('change-password.form') }}">Change Password</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else {{-- If user is not logged in --}}
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

    <main class="container py-4 flex-grow-1"> {{-- Use main tag, add padding, allow grow --}}
        {{-- Removed static alert boxes for session status/errors --}}

        {{-- Main page content goes here --}}
        @yield('content')

    </main> <!-- End main container -->

    {{-- === START: Toast Container HTML === --}}
    <div id="toast-container"></div>
    {{-- === END: Toast Container HTML === --}}


    <footer class="mt-auto py-3 bg-light"> {{-- Bootstrap footer classes --}}
        <div class="container text-center">
             <p class="text-muted mb-0">Guided Tours Org © {{ date('Y') }}</p> {{-- mb-0 removes default bottom margin --}}
        </div>
    </footer>

    {{-- @vite directive includes Bootstrap JS which handles dropdowns etc. --}}
    {{-- It's generally better to put scripts at the end --}}

    @stack('scripts') {{-- Placeholder for page-specific scripts --}}
</body>
</html>
