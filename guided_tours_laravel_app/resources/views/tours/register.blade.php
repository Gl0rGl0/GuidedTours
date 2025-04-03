@extends('layouts.app')

@section('title', 'Register for Tour')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ __('Register Interest for Tour') }}</div>
            <div class="card-body">

                {{-- TODO: Fetch and display details about the specific visit --}}
                <p><strong>Visit ID:</strong> {{ $visit_id ?? 'Not specified' }}</p> {{-- Example: Get visit ID from route --}}
                {{-- Display Visit Title, Place, Date etc. here once fetched in controller --}}

                <p class="mt-3">This form will allow users (fruitori) to register their interest or book a spot for this tour visit.</p>

                <p><em>(Functionality to be implemented)</em></p>

                {{-- TODO: Add registration form here --}}
                {{-- Example form structure --}}
                {{--
                <form action="{{ route('register-tour.submit') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="visit_id" value="{{ $visit_id ?? '' }}">
                    <div class="mb-3">
                        <label for="num_participants" class="form-label">Number of Participants:</label>
                        <input type="number" id="num_participants" name="num_participants" class="form-control" style="width: 100px;" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Registration</button>
                </form>
                --}}
            </div>
        </div>
    </div>
</div>
@endsection
