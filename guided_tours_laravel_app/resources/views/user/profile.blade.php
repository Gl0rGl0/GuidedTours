@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
    <h2>User Profile</h2>
    <p>Welcome, {{ $user->username }}!</p> {{-- Access user object passed from controller --}}
    <p>This page will allow you to view and potentially edit your profile information.</p>
    <p><em>(Functionality to be implemented)</em></p>

    {{-- Example: Display user role --}}
    <p>Your current role: {{ $user->role }}</p>

    {{-- TODO: Add form to edit profile details if required --}}
@endsection
