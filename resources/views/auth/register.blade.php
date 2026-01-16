@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center min-vh-50 align-items-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-4">
             <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">Create Account</h2>
                    <p class="text-muted small">Join us to book your guided tours</p>
                </div>

                <form action="{{ route('register') }}" method="post">
                    @csrf
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" placeholder="Choose a Username" value="{{ old('username') }}" required autofocus>
                        <label for="username">Username</label>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Create a Password" required>
                        <label for="password">Password</label>
                         @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text small">Must be at least 8 characters.</div>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                        <label for="password_confirmation">Confirm Password</label>
                    </div>
                    
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                            Register <i class="bi bi-person-plus ms-2"></i>
                        </button>
                    </div>
                    
                     <div class="text-center">
                        <p class="small text-muted mb-0">Already have an account? <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
