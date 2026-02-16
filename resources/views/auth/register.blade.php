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
                    
                    <div class="form-floating mb-4 position-relative">
                        <input type="password"
                            class="form-control pe-5 @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Password"
                            minlength="8"
                            required>
                        <label for="password">Password</label>
                        <div class="form-text text-muted small"><i class="bi bi-info-circle me-1"></i>Min. 8 characters</div>

                        <span class="password-toggle"
                            onmousedown="showPassword('password')"
                            onmouseup="hidePassword('password')"
                            onmouseleave="hidePassword('password')">
                            <i class="bi bi-eye"></i>
                        </span>

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
                            minlength="8"
                            required>
                        <label for="password_confirmation">Confirm Password</label>

                        <span class="password-toggle"
                            onmousedown="showPassword('password_confirmation')"
                            onmouseup="hidePassword('password_confirmation')"
                            onmouseleave="hidePassword('password_confirmation')">
                            <i class="bi bi-eye"></i>
                        </span>
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


<script>
function showPassword(id) {
    const pw = document.getElementById(id);
    if (!pw) return;
    pw.type = 'text';

    const icon = pw.parentElement.querySelector('.password-toggle i');
    if (icon) icon.className = 'bi bi-eye-slash';
}

function hidePassword(id) {
    const pw = document.getElementById(id);
    if (!pw) return;
    pw.type = 'password';

    const icon = pw.parentElement.querySelector('.password-toggle i');
    if (icon) icon.className = 'bi bi-eye';
}
</script>
