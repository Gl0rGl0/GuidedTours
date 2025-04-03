@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{ __('Login') }}</div>
            <div class="card-body">
                <p class="card-text text-center mb-4">Please enter your credentials to access your dashboard.</p>

                {{-- Display general status message (e.g., after registration) --}}
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Display Login Errors --}}
                @if ($errors->has('username')) {{-- Check specifically for the username/password error --}}
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->first('username') }}
                    </div>
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
                    <div class="mb-3">
                        <label for="username" class="form-label">{{ __('Username') }}</label>
                        {{-- Use old() helper to retain input on error --}}
                        <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                         @error('password') {{-- Display password specific errors if needed --}}
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    {{-- Optional: Remember Me Checkbox --}}
                    {{-- <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                    </div> --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
                    </div>
                </form>
                <p class="mt-3 text-center">Don't have an account? <a href="{{ route('register') }}">Register here</a> (Users/Fruitori only)</p>
            </div>
        </div>
    </div>
</div>
@endsection
