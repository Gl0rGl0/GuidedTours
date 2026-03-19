@extends('layouts.app')

@section('title', __('messages.user.change_password.page_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 text-center">
                     <h4 class="fw-bold text-primary mb-1">{{ __('messages.user.change_password.title') }}</h4>
                     <p class="text-muted small">{{ __('messages.user.change_password.description') }}</p>
                </div>
                
                <div class="card-body p-4 pt-3">
                    <form action="{{ route('change-password.update') }}" method="POST">
                        @csrf
                        
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" id="current_password" name="current_password" class="form-control pe-5 @error('current_password') is-invalid @enderror" placeholder="{{ __('messages.user.change_password.current_password') }}" required autofocus>
                            <label for="current_password">{{ __('messages.user.change_password.current_password') }}</label>
                            <button type="button"
                                class="btn position-absolute top-50 end-0 translate-middle-y text-muted px-3 border-0 shadow-none z-3"
                                tabindex="-1" onclick="togglePassword('current_password', this)">
                                <i class="bi bi-eye password-toggle"></i>
                            </button>
                             @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-floating position-relative">
                                <input type="password" id="new_password" name="new_password" class="form-control pe-5 @error('new_password') is-invalid @enderror" placeholder="{{ __('messages.user.change_password.new_password') }}" required minlength="8">
                                <label for="new_password">{{ __('messages.user.change_password.new_password') }}</label>

                                <button type="button"
                                    class="btn position-absolute top-50 end-0 translate-middle-y text-muted px-3 border-0 shadow-none z-3"
                                    tabindex="-1" onclick="togglePassword('new_password', this)">
                                    <i class="bi bi-eye password-toggle"></i>
                                </button>
                            </div>
                            <div class="form-text small text-muted" id="passwordHelp">
                                <i class="bi bi-info-circle me-1"></i>{{ __('messages.user.change_password.min_characters') }}
                            </div>
                             @error('new_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-floating position-relative">
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control pe-5" placeholder="{{ __('messages.user.change_password.confirm') }}" required minlength="8">
                                <label for="new_password_confirmation">{{ __('messages.user.change_password.confirm_new_password') }}</label>

                                <button type="button"
                                    class="btn position-absolute top-50 end-0 translate-middle-y text-muted px-3 border-0 shadow-none z-3"
                                    tabindex="-1" onclick="togglePassword('new_password_confirmation', this)">
                                    <i class="bi bi-eye password-toggle"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted small d-none" id="confirmHelp">
                                <i class="bi bi-check-circle me-1"></i>{{ __('messages.auth.register.passwords_match') }}
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm" id="submitBtn">
                                {{ __('messages.user.change_password.submit_btn') }}
                            </button>
                        </div>
                        
                         <div class="text-center">
                            <a href="{{ route('profile') }}" class="text-decoration-none text-muted small">
                                <i class="bi bi-arrow-left me-1"></i> {{ __('messages.user.change_password.back_to_profile') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        setupFormValidation({
            passwordInputId: 'new_password',
            passwordHelpId: 'passwordHelp',
            confirmInputId: 'new_password_confirmation',
            confirmHelpId: 'confirmHelp',
            submitBtnId: 'submitBtn',
            passwordMinLength: 8,
            requireComplexPassword: true
        });
    });
</script>
