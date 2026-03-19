@extends('layouts.app')

@section('title', __('messages.auth.login.page_title'))

@section('content')
<div class="row justify-content-center min-vh-50 align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">{{ __('messages.auth.login.title') }}</h2>
                    <p class="text-muted small">{{ __('messages.auth.login.description') }}</p>
                </div>
                
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    
                    <div class="mb-3">
                        <div class="form-floating position-relative">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ __('messages.auth.login.email_label') }}" value="{{ old('email') }}" required autofocus>
                            <label for="email">{{ __('messages.auth.login.email_label') }}</label>
                        </div>
                        <div class="form-text text-muted small d-none" id="emailHelp"></div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-4 position-relative">
                        <input type="password"
                            class="form-control pe-5 @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="{{ __('messages.auth.login.password_label') }}"
                            required>

                        <label for="password">{{ __('messages.auth.login.password_label') }}</label>

                        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y text-muted px-3 border-0 shadow-none z-3" tabindex="-1" onclick="togglePassword('password', this)" id="togglePasswordBtn">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end mb-4">
                        <a href="{{ route('password.request') }}" class="small text-decoration-none text-muted">{{ __('messages.auth.login.forgot_password') }}</a>
                    </div>

                    
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm" id="submitBtn">
                            {{ __('messages.auth.login.submit_btn') }} <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="small text-muted mb-0">{{ __('messages.auth.login.no_account') }} <a href="{{ route('register') }}" class="fw-bold text-decoration-none">{{ __('messages.auth.login.register') }}</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    // We only need to check email format and ensure password is not empty.
    // togglePassword is now globally available in validation.js.
    setupFormValidation({
        emailInputId: 'email',
        emailHelpId: 'emailHelp',
        passwordInputId: 'password',
        submitBtnId: 'submitBtn',
        requireComplexPassword: false // Simple requirement for login
    });
});
</script>
