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
            top: 1.5rem;
            right: 1.5rem;
            z-index: 1055; /* Ensure it's above Bootstrap modals (usually 1050) */
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            pointer-events: none; /* Allow clicks to pass through the container */
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
                <ul class="navbar-nav ms-auto">
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

    <main class="container py-4 flex-grow-1">
        @yield('content')
    </main> 

    {{-- === START: Toast Container HTML === --}}
    <div id="toast-container"></div>
    {{-- === END: Toast Container HTML === --}}


    <footer class="mt-auto py-3 bg-light"> {{-- Bootstrap footer classes --}}
        <div class="container text-center">
             <p class="text-muted mb-0">Guided Tours Org © {{ date('Y') }}</p>
        </div>
    </footer>
    {{-- === START: Centralized Toast JavaScript === --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastContainer = document.getElementById('toast-container');

            // Make showToast globally accessible
            window.showToast = function(text, type = 'info', duration = 5000) { // type can be 'success', 'error', 'warning', 'info'
                if (!text || !toastContainer) {
                    console.warn('Toast container not found or no text provided.');
                    return;
                };

                const toast = document.createElement('div');
                // Use the specific class and add type-specific class
                toast.className = `toast-notification ${type}`;
                toast.textContent = text;
                toast.setAttribute('role', 'alert'); // Accessibility
                toast.setAttribute('aria-live', 'assertive'); // Accessibility: changes announced immediately

                toastContainer.appendChild(toast);

                // Trigger reflow/styles application before adding 'show' class for transition
                requestAnimationFrame(() => {
                    // Force calculation (read property) - helps ensure transition works
                    void toast.offsetWidth;
                    requestAnimationFrame(() => {
                        toast.classList.add('show');
                    });
                });


                // Set timeout to hide and remove toast
                const hideTimeout = setTimeout(() => {
                    toast.classList.remove('show');
                    // Remove the element after the transition completes
                    toast.addEventListener('transitionend', () => {
                         // Double check it's still a child before removing
                         if (toast.parentNode === toastContainer) {
                            toastContainer.removeChild(toast);
                         }
                    }, { once: true }); // Ensure listener runs only once

                     // Fallback removal if transitionend doesn't fire (e.g., interrupted)
                     // Use duration + transition time + buffer
                     setTimeout(() => {
                         if (toast.parentNode === toastContainer) {
                            console.warn('Toast fallback removal triggered for:', text);
                            toastContainer.removeChild(toast);
                         }
                     }, 600); // Should be slightly longer than CSS transition duration (0.5s)

                }, duration); // Use configurable duration
            }

            // Automatically show toasts based on Laravel Session Flash Data on page load
            // Check for multiple common keys
            @if (session('status'))
                window.showToast("{{ session('status') }}", 'success'); // Often used for general success
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

            // Show first validation error in a toast (consider if this is too intrusive)
            @if ($errors->any())
                window.showToast("{{ $errors->first() }}", 'error');
            @endif

        });
    </script>
    {{-- === END: Centralized Toast JavaScript === --}}

    @stack('scripts') {{-- Placeholder for page-specific scripts --}}
</body>
</html>
