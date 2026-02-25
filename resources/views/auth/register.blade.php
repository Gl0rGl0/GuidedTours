@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="row justify-content-center min-vh-50 align-items-center mt-4">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Create Account</h2>
                        <p class="text-muted small mb-1">Join us to book your guided tours</p>
                    </div>

                    <form action="{{ route('register') }}" method="post">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                id="first_name" name="first_name" placeholder="First Name" value="{{ old('first_name') }}"
                                required autofocus>
                            <label for="first_name">First Name</label>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                                name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" required>
                            <label for="last_name">Last Name</label>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-floating position-relative">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                                <label for="email">Email</label>
                            </div>
                            <div class="form-text text-muted small d-none" id="emailHelp"></div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-floating position-relative">
                                <input type="password" class="form-control pe-5 @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Password" minlength="6" required>
                                <label for="password">Password</label>

                                <button type="button"
                                    class="btn position-absolute top-50 end-0 translate-middle-y text-muted px-3 border-0 shadow-none z-3"
                                    tabindex="-1" onclick="togglePassword('password', this)">
                                    <i class="bi bi-eye password-toggle"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted small" id="passwordHelp">
                                <i class="bi bi-info-circle me-1"></i>Min. 6 characters
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-floating position-relative">
                                <input type="password" class="form-control pe-5" id="password_confirmation"
                                    name="password_confirmation" placeholder="Confirm Password" minlength="6" required>
                                <label for="password_confirmation">Confirm Password</label>

                                <button type="button"
                                    class="btn position-absolute top-50 end-0 translate-middle-y text-muted px-3 border-0 shadow-none z-3"
                                    tabindex="-1" onclick="togglePassword('password_confirmation', this)">
                                    <i class="bi bi-eye password-toggle"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted small d-none" id="confirmHelp">
                                <i class="bi bi-check-circle me-1"></i>Passwords match
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm" id="submitBtn">
                                Register <i class="bi bi-person-plus ms-2"></i>
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="small text-muted mb-0">Already have an account? <a href="{{ route('login') }}"
                                    class="fw-bold text-decoration-none">Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const password = document.getElementById('password');
        const confirm = document.getElementById('password_confirmation');
        const email = document.getElementById('email');
        const passHelp = document.getElementById('passwordHelp');
        const confHelp = document.getElementById('confirmHelp');
        const emailHelp = document.getElementById('emailHelp');
        const submitBtn = document.getElementById('submitBtn');

        let pValid = false;
        let cValid = false;
        let eValid = (email.value.trim() !== '' && validateEmailFormat(email.value.trim())); // Check on load (e.g., from old() input)
        let typingTimer;

        // Simple strict email regex
        function validateEmailFormat(mail) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail);
        }

        function validatePasswords() {
            const pVal = password.value;
            const cVal = confirm.value;
            pValid = false;
            cValid = false;

            if (pVal.length >= 6) {
                passHelp.className = 'form-text text-success small';
                passHelp.innerHTML = '<i class="bi bi-check-circle me-1"></i>Min. 6 characters';
                pValid = true;
            } else if (pVal.length > 0) {
                passHelp.className = 'form-text text-danger small';
                passHelp.innerHTML = '<i class="bi bi-x-circle me-1"></i>Min. 6 characters';
            } else {
                passHelp.className = 'form-text text-muted small';
                passHelp.innerHTML = '<i class="bi bi-info-circle me-1"></i>Min. 6 characters';
            }

            if (cVal.length > 0) {
                confHelp.classList.remove('d-none');
                if (pVal === cVal) {
                    confHelp.className = 'form-text text-success small';
                    confHelp.innerHTML = '<i class="bi bi-check-circle me-1"></i>Passwords match';
                    cValid = true;
                } else {
                    confHelp.className = 'form-text text-danger small';
                    confHelp.innerHTML = '<i class="bi bi-x-circle me-1"></i>Passwords do not match';
                }
            } else {
                confHelp.classList.add('d-none');
            }

            updateSubmitStatus();
        }

        function updateSubmitStatus() {
            submitBtn.disabled = !(pValid && cValid && eValid);
        }

        // Email Real-time validation
        function handleEmailInput() {
            clearTimeout(typingTimer);
            const val = email.value.trim();
            
            if (val.length === 0) {
                emailHelp.classList.add('d-none');
                eValid = false;
                updateSubmitStatus();
                return;
            }

            // Immediately clear error if they start typing again
            emailHelp.classList.add('d-none');
            
            // Only validate when they stop typing for 600ms
            typingTimer = setTimeout(() => {
                if (validateEmailFormat(val)) {
                    emailHelp.classList.remove('d-none');
                    emailHelp.className = 'form-text text-success small';
                    emailHelp.innerHTML = '<i class="bi bi-check-circle me-1"></i>Valid email format';
                    eValid = true;
                } else {
                    emailHelp.classList.remove('d-none');
                    emailHelp.className = 'form-text text-danger small';
                    emailHelp.innerHTML = '<i class="bi bi-x-circle me-1"></i>Please enter a valid email address';
                    eValid = false;
                }
                updateSubmitStatus();
            }, 600);
        }

        // Standard instant validation (password logic)
        password.addEventListener('input', validatePasswords);
        confirm.addEventListener('input', validatePasswords);
        
        // Debounced email validation
        email.addEventListener('input', handleEmailInput);

        // Immediate check on unfocus (blur)
        email.addEventListener('blur', () => {
             clearTimeout(typingTimer);
             const val = email.value.trim();
             if (val.length > 0) {
                 if (validateEmailFormat(val)) {
                    emailHelp.classList.remove('d-none');
                    emailHelp.className = 'form-text text-success small';
                    emailHelp.innerHTML = '<i class="bi bi-check-circle me-1"></i>Valid email format';
                    eValid = true;
                } else {
                    emailHelp.classList.remove('d-none');
                    emailHelp.className = 'form-text text-danger small';
                    emailHelp.innerHTML = '<i class="bi bi-x-circle me-1"></i>Please enter a valid email address';
                    eValid = false;
                }
                updateSubmitStatus();
             }
        });

        // Initialize state
        validatePasswords();
        if(email.value.trim().length > 0) {
             email.dispatchEvent(new Event('blur')); // Trigger initial visual validation if they failed registration once and their email is prepopulated via old()
        }
    });
</script>