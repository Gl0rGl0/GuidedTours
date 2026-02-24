@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center min-vh-50 align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">Welcome Back</h2>
                    <p class="text-muted small">Please login to continue</p>
                </div>
                
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required autofocus>
                        <label for="email">Email Address</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-4 position-relative">
                        <input type="password"
                            class="form-control pe-5 @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            value="password123"
                            placeholder="Password"
                            required>

                        <label for="password">Password</label>

                        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y text-muted px-3 border-0 shadow-none z-3" tabindex="-1" onclick="togglePassword()" id="togglePasswordBtn">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-end mb-4">
                        <a href="{{ route('password.request') }}" class="small text-decoration-none text-muted">Forgot password?</a>
                    </div>

                    
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                            Login <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="small text-muted mb-0">Don't have an account? <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Register</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>

function togglePassword() {
    const pw = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    
    if (pw.type === 'password') {
        pw.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        pw.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

</script>
