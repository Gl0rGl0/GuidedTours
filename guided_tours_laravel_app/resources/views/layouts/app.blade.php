<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> {{-- Use Laravel's locale --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guided Tours')</title> {{-- Allow overriding title --}}
    {{-- Use asset() helper for CSS, assuming it will be in public/css --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    {{-- Add CSRF Token for forms --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Add any other common head elements here (e.g., scripts, other CSS) --}}
    @stack('styles') {{-- Placeholder for page-specific styles --}}
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><a href="{{ route('home') }}">Guided Tours Org</a></h1> {{-- Use route name --}}
            </div>
            <nav>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    @auth {{-- Check if user is logged in --}}
                        @if (Auth::user()->role === 'configurator') {{-- Check user role --}}
                            <li><a href="{{ route('admin.configurator') }}">Admin Panel</a></li>
                        @endif
                        {{-- User Dropdown --}}
                        <li class="user-menu">
                            <a href="#" class="username-trigger">{{ Auth::user()->username }} <span class="arrow">&#9662;</span></a>
                            <ul class="dropdown-content">
                                <li><a href="{{ route('profile') }}">Profile</a></li>
                                <li><a href="{{ route('change-password.form') }}">Change Password</a></li>
                                <li>
                                    {{-- Logout needs a form for POST request --}}
                                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                        @csrf
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                                            Logout
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else {{-- If user is not logged in --}}
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        {{-- Session Status/Error Messages --}}
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Main page content goes here --}}
        @yield('content')
    </div> <!-- End .container -->

    <footer>
        <p>Guided Tours Org &copy; {{ date('Y') }}</p> {{-- Use Blade echo --}}
    </footer>

    {{-- Add common scripts here --}}
    @stack('scripts') {{-- Placeholder for page-specific scripts --}}
</body>
</html>
