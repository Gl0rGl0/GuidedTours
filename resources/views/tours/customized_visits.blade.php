@extends('layouts.app')

@section('title', 'Custom Visits')

@section('content')
    @php
        $user = Auth::user();
        $pageTitle = 'Visits';
        if ($user->hasRole('Admin')) $pageTitle = 'Past Visits Archive';
        if ($user->hasRole('Guide')) $pageTitle = 'My Assigned Visits';
        if ($user->hasRole('Customer')) $pageTitle = 'My Past Visits';
    @endphp

    @if($user->hasRole('Guide'))
        <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex gap-3 align-items-start">
            <i class="bi bi-info-circle-fill fs-4 mt-1 flex-shrink-0"></i>
            <div>
                <strong>Guide instructions</strong>
                <ul class="mb-0 mt-1 small">
                    <li>Arrive at the meeting point <strong>10 minutes early</strong> to welcome participants.</li>
                    <li>Check each participant's <strong>booking code</strong> before starting the tour.</li>
                    <li>If a participant has a ticket requirement, verify it at the entrance.</li>
                    <li>In case of issues, contact the organisation immediately.</li>
                </ul>
            </div>
        </div>
    @endif

    <div class="d-flex align-items-center mb-4 border-bottom pb-2">
        <h2 class="fw-bold text-primary mb-0 me-3">{{ $pageTitle }}</h2>
    </div>

    @if($visits->isEmpty())
        <div class="text-center py-5 card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <i class="bi bi-clock-history display-4 text-muted opacity-25 mb-3"></i>
                <h5 class="text-muted">No records found</h5>
                <p class="text-muted small mb-0">There are no visits to display in this category.</p>
                 <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill mt-3">Back to Home</a>
            </div>
        </div>
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
                     <div class="card h-100 shadow-sm border-0 card-hover rounded-4">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 text-uppercase" style="font-size: 0.7rem;">
                                    {{ $visit->status }}
                                </span>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') }}</small>
                            </div>

                             <h5 class="card-title fw-bold mb-1">{{ $visit->visitType->title }}</h5>
                             <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i> {{ $visit->visitType->place->name }}</p>

                            <ul class="list-unstyled text-muted small mb-4 flex-grow-1">
                                <li class="mb-2"><i class="bi bi-clock me-2 text-secondary"></i> {{ \Carbon\Carbon::parse($visit->visitType->start_time)->format('g:i A') }}</li>
                                <li class="mb-2"><i class="bi bi-map me-2 text-secondary"></i> {{ $visit->visitType->meeting_point }}</li>
                                @if($visit->assignedVolunteer && $visit->assignedVolunteer->id !== $user->id)
                                    <li class="mb-2"><i class="bi bi-person-badge me-2 text-secondary"></i> Vol: {{ $visit->assignedVolunteer->first_name }} {{ $visit->assignedVolunteer->last_name }}</li>
                                @endif
                                 <li><i class="bi bi-people me-2 text-secondary"></i> {{ $visit->registrations->sum('num_participants') }} Attendees</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
