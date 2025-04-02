@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
    <h2>Change Password</h2>

    {{-- Display Success Message --}}
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('change-password.update') }}" method="POST">
        @csrf
        <div>
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        <div>
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required minlength="6">
        </div>
        <div>
            <label for="new_password_confirmation">Confirm New Password:</label> {{-- Name must be new_password_confirmation --}}
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
        </div>
        <div>
            <button type="submit">Change Password</button>
        </div>
    </form>

    {{-- Basic form styling - consider moving to style.css --}}
    <style>
        form div { margin-bottom: 10px; }
        label { display: block; margin-bottom: 5px; }
        input[type="password"] { width: 250px; padding: 8px; }
        button { padding: 10px 15px; background-color: #333; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #555; }
        .alert { border: 1px solid; padding: 10px; margin-bottom: 15px; }
        .alert-danger { color: red; border-color: red; }
        .alert-success { color: green; border-color: green; }
    </style>
@endsection
