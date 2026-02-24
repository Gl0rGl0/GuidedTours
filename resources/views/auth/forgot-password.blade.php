@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="row justify-content-center min-vh-50 align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">Forgot Password?</h2>
                    <p class="text-muted small">Enter your registered email address and we will send you a link to reset your password.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 border-0 shadow-sm" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <form action="{{ route('password.email') }}" method="post">
                    @csrf
                    
                    <div class="form-floating mb-4">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                        <label for="email">Email address</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm d-flex align-items-center justify-content-center">
                            <i class="bi bi-envelope-paper me-2"></i> Send Reset Link
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="small text-muted mb-0">Remembered your password? <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Back to Login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
