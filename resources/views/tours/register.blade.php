@extends('layouts.app')

@section('title', 'Register for Tour')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ __('Register Interest for Tour') }}</div>
            <div class="card-body">
                @if ($visit)
                    <h4>{{ $visit->visitType->title }}</h4>
                    <p><strong>Place:</strong> {{ $visit->visitType->place->name }}</p>
                    <p><small class="text-muted">{{ $visit->visitType->place->location }}</small></p>
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($visit->visit_date)->format('D, M j, Y') }}</p>
                    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($visit->visitType->start_time)->format('g:i A') }} ({{ $visit->visitType->duration_minutes }} mins)</p>
                    <p><strong>Meeting Point:</strong> {{ $visit->visitType->meeting_point }}</p>
                    <p><strong>Description:</strong> {!! nl2br(e($visit->visitType->description)) !!}</p>

                    <p><strong>Current Subscribers:</strong> {{ $visit->registrations->sum('num_participants') }} / {{ $visit->visitType->max_participants }}</p>

                    @if ($visit->visitType->requires_ticket)
                        <p class="card-text"><small><em>Note: An entrance ticket purchase may be required.</em></small></p>
                    @endif

                    <hr>

                    <p>Please enter the number of participants for your registration.</p>

                    <form action="{{ route('visits.register.submit', ['visit' => $visit->visit_id]) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label for="num_participants" class="form-label">Number of Participants:</label>
                            <input type="number" id="num_participants" name="num_participants" class="form-control" style="width: 100px;" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Registration</button>
                    </form>
                @else
                    <p>Tour visit details could not be loaded.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
