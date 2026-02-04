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
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" placeholder="Username" required autofocus>
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
                            value="password123"
                            placeholder="Password"
                            required>

                        <label for="password">Password</label>

                        <span class="password-toggle"
                            onmousedown="showPassword()"
                            onmouseup="hidePassword()"
                            onmouseleave="hidePassword()">
                            <i class="bi bi-eye"></i>
                        </span>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
</div>
@endsection

<script>

function showPassword() {
    const pw = document.getElementById('password');
    pw.type = 'text';
    pw.nextElementSibling.nextElementSibling.innerHTML = '<i class="bi bi-eye-slash"></i>';
}

function hidePassword() {
    const pw = document.getElementById('password');
    pw.type = 'password';
    pw.nextElementSibling.nextElementSibling.innerHTML = '<i class="bi bi-eye"></i>';
}

</script>
