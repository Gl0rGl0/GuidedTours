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
    @stack('styles') {{-- Placeholder for page-specific styles --}}
</head>
<body>
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
                        @if (Auth::user()->role === 'configurator') {{-- Check user role --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Route::is('admin.configurator') ? 'active' : '' }}" href="{{ route('admin.configurator') }}">Admin Panel</a>
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

    <main class="container py-4"> {{-- Use main tag and add padding --}}
        {{-- Removed static alert boxes for session status/errors --}}

        {{-- Main page content goes here --}}
        @yield('content')
    </main> <!-- End main container -->

    <footer class="mt-auto py-3 bg-light"> {{-- Bootstrap footer classes --}}
        <div class="container text-center">
             <p class="text-muted">Guided Tours Org &copy; {{ date('Y') }}</p>
        </div>
    </footer>

    {{-- @vite directive includes Bootstrap JS which handles dropdowns etc. --}}
    @stack('scripts') {{-- Placeholder for page-specific scripts --}}
</body>
</html>
