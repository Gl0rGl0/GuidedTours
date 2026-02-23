@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="row justify-content-center min-vh-50 align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">Reset Password</h2>
                    <p class="text-muted small">Enter your new password below</p>
                </div>
                
                <form action="{{ route('password.update') }}" method="post">
                    @csrf
                    
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" value="{{ $email }}" disabled>
                        <label>Email Address</label>
                    </div>
                    
                    <div class="form-floating mb-3 position-relative">
                        <input type="password"
                            class="form-control pe-5 @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="New Password"
                            required>

                        <label for="password">New Password</label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input type="password"
                            class="form-control pe-5"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Confirm Password"
                            required>

                        <label for="password_confirmation">Confirm Password</label>
                    </div>
                    
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                            Reset Password <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
