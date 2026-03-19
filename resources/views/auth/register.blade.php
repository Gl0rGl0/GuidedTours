@extends('layouts.app')

@section('title', __('messages.auth.register.page_title'))

@section('content')
    <div class="row justify-content-center min-vh-50 align-items-center mt-4">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">{{ __('messages.auth.register.title') }}</h2>
                        <p class="text-muted small mb-1">{{ __('messages.auth.register.description') }}</p>
                    </div>

                    <form action="{{ route('register') }}" method="post">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                id="first_name" name="first_name" placeholder="{{ __('messages.auth.register.first_name') }}" value="{{ old('first_name') }}"
                                required autofocus>
                            <label for="first_name">{{ __('messages.auth.register.first_name') }}</label>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                                name="last_name" placeholder="{{ __('messages.auth.register.last_name') }}" value="{{ old('last_name') }}" required>
                            <label for="last_name">{{ __('messages.auth.register.last_name') }}</label>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-floating position-relative">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                                <label for="email">{{ __('messages.auth.register.email') }}</label>
                            </div>
                            <div class="form-text text-muted small d-none" id="emailHelp"></div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-floating position-relative">
                                <input type="password" class="form-control pe-5 @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="{{ __('messages.auth.register.password') }}" minlength="8" required>
                                <label for="password">{{ __('messages.auth.register.password') }}</label>

                                <button type="button"
                                    class="btn position-absolute top-50 end-0 translate-middle-y text-muted px-3 border-0 shadow-none z-3"
                                    tabindex="-1" onclick="togglePassword('password', this)">
                                    <i class="bi bi-eye password-toggle"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted small" id="passwordHelp">
                                <i class="bi bi-info-circle me-1"></i>{{ __('messages.auth.register.min_characters') }}
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-floating position-relative">
                                <input type="password" class="form-control pe-5" id="password_confirmation"
                                    name="password_confirmation" placeholder="{{ __('messages.auth.register.confirm_password') }}" minlength="8" required>
                                <label for="password_confirmation">{{ __('messages.auth.register.confirm_password') }}</label>

                                <button type="button"
                                    class="btn position-absolute top-50 end-0 translate-middle-y text-muted px-3 border-0 shadow-none z-3"
                                    tabindex="-1" onclick="togglePassword('password_confirmation', this)">
                                    <i class="bi bi-eye password-toggle"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted small d-none" id="confirmHelp">
                                <i class="bi bi-check-circle me-1"></i>{{ __('messages.auth.register.passwords_match') }}
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm" id="submitBtn">
                                {{ __('messages.auth.register.submit_btn') }} <i class="bi bi-person-plus ms-2"></i>
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="small text-muted mb-0">{{ __('messages.auth.register.already_account') }} <a href="{{ route('login') }}"
                                    class="fw-bold text-decoration-none">{{ __('messages.auth.register.login') }}</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        setupFormValidation({
            emailInputId: 'email',
            emailHelpId: 'emailHelp',
            passwordInputId: 'password',
            passwordHelpId: 'passwordHelp',
            confirmInputId: 'password_confirmation',
            confirmHelpId: 'confirmHelp',
            submitBtnId: 'submitBtn',
            passwordMinLength: 8,
            requireComplexPassword: true
        });
    });
</script>