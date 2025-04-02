@extends('layouts.app')

@section('title', 'Register for Tour')

@section('content')
    <h2>Register Interest for Tour</h2>

    {{-- TODO: Fetch and display details about the specific visit --}}
    <p>Visit ID: {{ $visit_id ?? 'Not specified' }}</p> {{-- Example: Get visit ID from route --}}

    <p>This form will allow users (fruitori) to register their interest or book a spot for a specific tour visit.</p>

    <p><em>(Functionality to be implemented)</em></p>

    {{-- TODO: Add registration form here --}}
    {{-- Example form structure --}}
    {{-- <form action="{{ route('register-tour.submit') }}" method="POST">
        @csrf
        <input type="hidden" name="visit_id" value="{{ $visit_id ?? '' }}">
        <div>
            <label for="num_participants">Number of Participants:</label>
            <input type="number" id="num_participants" name="num_participants" min="1" required>
        </div>
        <div>
            <button type="submit">Submit Registration</button>
        </div>
    </form> --}}
@endsection
