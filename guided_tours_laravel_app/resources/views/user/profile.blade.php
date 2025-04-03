@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ __('User Profile') }}</div>
            <div class="card-body">
                <p>Welcome, <strong>{{ $user->username }}</strong>!</p>
                <p>This page will allow you to view and potentially edit your profile information.</p>
                <p><em>(Functionality to be implemented)</em></p>

                <hr>

                {{-- Example: Display user role --}}
                <p><strong>Your current role:</strong> <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span></p>

                {{-- TODO: Add form to edit profile details if required --}}
                {{-- TODO: Display user's registrations or volunteer schedule if applicable --}}

                <div class="mt-4">
                     <a href="{{ route('change-password.form') }}" class="btn btn-secondary">Change Password</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
