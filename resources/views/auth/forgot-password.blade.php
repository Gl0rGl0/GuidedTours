@extends('layouts.app')

@section('title', __('messages.auth.forgot_password.page_title'))

@section('content')
<div class="row justify-content-center min-vh-50 align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">{{ __('messages.auth.forgot_password.title') }}</h2>
                    <p class="text-muted small">{{ __('messages.auth.forgot_password.description') }}</p>
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
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ __('messages.auth.forgot_password.email_placeholder') }}" value="{{ old('email') }}" required autofocus>
                        <label for="email">{{ __('messages.auth.forgot_password.email_label') }}</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm d-flex align-items-center justify-content-center">
                            <i class="bi bi-envelope-paper me-2"></i> {{ __('messages.auth.forgot_password.submit_btn') }}
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="small text-muted mb-0">{{ __('messages.auth.forgot_password.remembered_password') }} <a href="{{ route('login') }}" class="fw-bold text-decoration-none">{{ __('messages.auth.forgot_password.back_to_login') }}</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
