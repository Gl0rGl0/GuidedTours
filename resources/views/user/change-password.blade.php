@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 text-center">
                     <h4 class="fw-bold text-primary mb-1">Security</h4>
                     <p class="text-muted small">Update your password</p>
                </div>
                
                <div class="card-body p-4 pt-3">
                    <form action="{{ route('change-password.update') }}" method="POST">
                        @csrf
                        
                        <div class="form-floating mb-3">
                            <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Current Password" required autofocus>
                            <label for="current_password">Current Password</label>
                             @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" id="new_password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" placeholder="New Password" required minlength="6">
                            <label for="new_password">New Password</label>
                             @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Minimum 6 characters</div>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" placeholder="Confirm" required>
                            <label for="new_password_confirmation">Confirm New Password</label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                Update Password
                            </button>
                        </div>
                        
                         <div class="text-center">
                            <a href="{{ route('profile') }}" class="text-decoration-none text-muted small">
                                <i class="bi bi-arrow-left me-1"></i> Back to Profile
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
