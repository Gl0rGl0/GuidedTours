@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <h2>Login</h2>
    <p>Please enter your credentials to access your dashboard.</p>

    {{-- Display general status message (e.g., after registration) --}}
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    {{-- Display Login Errors --}}
    @if ($errors->has('username')) {{-- Check specifically for the username/password error --}}
        <p style="color: red; font-weight: bold;">{{ $errors->first('username') }}</p>
    @endif
    {{-- Display other validation errors if needed --}}
     @if ($errors->any() && !$errors->has('username'))
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('login') }}" method="post">
        @csrf {{-- Add CSRF token --}}
        <div>
            <label for="username">Username:</label>
            {{-- Use old() helper to retain input on error --}}
            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        {{-- Optional: Remember Me Checkbox --}}
        {{-- <div>
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Remember Me</label>
        </div> --}}
        <div>
            <button type="submit">Login</button>
        </div>
    </form>
    <p>Don't have an account? <a href="{{ route('register') }}">Register here</a> (Users/Fruitori only)</p>
@endsection
