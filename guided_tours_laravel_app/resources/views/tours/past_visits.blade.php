@extends('layouts.app')

@section('title', 'Past Visits')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Past Visits</h1>

    @if($pastVisits->isEmpty())
        <p>There are no past visits to display at the moment.</p>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($pastVisits as $visit)
                @php
                    $borderClass = match($visit->status) {
                        App\Models\Visit::STATUS_COMPLETE, App\Models\Visit::STATUS_EFFECTED => 'border-start border-4 border-success',
                        App\Models\Visit::STATUS_CANCELLED => 'border-start border-4 border-danger',
                        default => 'border-start border-4 border-secondary',
                    };
                    $badgeClass = match($visit->status) {
                        App\Models\Visit::STATUS_COMPLETE, App\Models\Visit::STATUS_EFFECTED => 'bg-success text-white',
                        App\Models\Visit::STATUS_CANCELLED => 'bg-danger text-white',
                        default => 'bg-secondary text-white',
                    };
                @endphp

                <div class="col">
                    <div class="card h-100 {{ $borderClass }}">
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
