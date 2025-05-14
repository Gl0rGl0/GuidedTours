@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{ __('Login') }}</div>
            <div class="card-body">
                <p class="card-text text-center mb-4">Please enter your credentials to access your dashboard.</p>
                <form action="{{ route('login') }}" method="post">
                    @csrf {{-- Add CSRF token --}}
                    <x-form-input
                        name="username"
                        label="{{ __('Username') }}"
                        type="text"
                        id="username"
                        required
                        autofocus
                    />
                    <x-form-input
                        name="password"
                        label="{{ __('Password') }}"
                        type="password"
                        id="password"
                        required
                    />
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
                    </div>
                </form>
                <p class="mt-3 text-center">Don't have an account? <a href="{{ route('register') }}">Register here</a> (Users/Fruitori only)</p>
            </div>
        </div>
    </div>
</div>
@endsection
