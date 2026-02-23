@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="row justify-content-center min-vh-50 align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="mb-4">
                    <i class="bi bi-tools text-primary display-4"></i>
                </div>
                <h2 class="fw-bold text-primary mb-3">Work in Progress</h2>
                <p class="text-muted mb-4">
                    The automated password recovery feature is currently under development. 
                    If you have lost access to your account, please contact an administrator to reset it for you.
                </p>
                <div class="d-grid mt-4">
                    <a href="{{ route('login') }}" class="btn btn-primary rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-2"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
