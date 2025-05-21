@extends('layouts.app')

@section('title', 'Custom Visits')

@section('content')
<div class="container my-5">
    @php
        $user = Auth::user();
    @endphp

    @if ($user->hasRole('configurator'))
        <h1 class="mb-4">Past Visits</h1>
    @endif
    @if ($user->hasRole('volunteer'))
        <h1 class="mb-4">Assigned Visits</h1>
    @endif
    @if ($user->hasRole('fruitore'))
        <h1 class="mb-4">My Past Visits</h1>
    @endif

    @if($visits->isEmpty())
        @if ($user->hasRole('configurator'))
            <p>There are no past visits to display at the moment.</p>
        @endif
        @if ($user->hasRole('volunteer'))
            <p>You haven't been assigned to any visits in the past.</p>
        @endif
        @if ($user->hasRole('fruitore'))
            <p>You haven't booked any visits in the past.</p>
        @endif
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($visits as $visit)
                @php
                    $badgeClass = match($visit->status) {
                        App\Models\Visit::STATUS_EFFECTED => 'bg-success text-white',
                        App\Models\Visit::STATUS_PROPOSED => 'bg-primary text-white',
                        App\Models\Visit::STATUS_CONFIRMED => 'bg-success text-white',
                        App\Models\Visit::STATUS_COMPLETE => 'bg-success-subtle text-white',
                        App\Models\Visit::STATUS_CANCELLED => 'bg-danger text-white',
                        default => 'bg-secondary text-white',
                    };
                @endphp

                <div class="col">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="card-title">{{ $visit->visitType->title }}</h5>
                                    <h6 class="card-subtitle text-muted">{{ $visit->visitType->place->name }}</h6>
                                </div>
                                <span class="badge {{ $badgeClass }} text-uppercase">
                                    {{ $visit->status }}
                                </span>
                            </div>

                            <ul class="list-unstyled mb-4 flex-grow-1">
                                <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($visit->visit_date)->format('D, M j, Y') }}</li>
                                <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($visit->visitType->start_time)->format('g:i A') }}</li>
                                <li><strong>Meeting:</strong> {{ $visit->visitType->meeting_point }}</li>
                                @if($visit->assignedVolunteer)
                                    <li><strong>Volunteer:</strong> {{ $visit->assignedVolunteer->username }}</li>
                                @endif
                            </ul>

                            <div class="mt-auto">
                                <p class="mb-0"><strong>Registrations:</strong> {{ $visit->registrations->sum('num_participants') }} / {{ $visit->visitType->max_participants }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
