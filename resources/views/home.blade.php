@extends('layouts.app')

@section('title', 'Home - Guided Tours')

@section('content')
    <h2>Welcome to the Guided Tours Portal</h2>
    <p>Discover amazing places and experiences offered by our dedicated volunteers.</p>
    <p>Explore the available tours below or use the navigation above to log in and manage your activities.</p>

    <hr>

    @if(session('error_message'))
        <p style="color: red;">{{ session('error_message') }}</p>
    @endif

    <h3>Proposed Tours</h3>
    @if($proposed_visits->isEmpty())
        <p>There are currently no proposed tours scheduled. Please check back later!</p>
    @else
        <p>Here are the upcoming guided tours open for registration:</p>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($proposed_visits as $tour)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $tour->visitType->title }}</h5>
                            <h6 class="card-subtitle mb-2">
                                <span class="badge {{ $tour->status === 'proposed' ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ ucfirst($tour->status)}}
                                </span>
                            </h6>
                            <p class="card-text">
                                <strong>Place:</strong> {{ $tour->visitType->place->name }}<br>
                                <small class="text-muted">{{ $tour->visitType->place->location }}</small>
                            </p>
                            <p class="card-text">
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($tour->visit_date)->format('D, M j, Y') }}<br>
                                <strong>Time:</strong> {{ \Carbon\Carbon::parse($tour->visitType->start_time)->format('g:i A') }} ({{ $tour->visitType->duration_minutes }} mins)<br>
                                <strong>Meeting:</strong> {{ $tour->visitType->meeting_point }}
                            </p>
                            <p class="card-text"><small>{!! nl2br(e($tour->visitType->description)) !!}</small></p>
                            @if ($tour->visitType->requires_ticket)
                                <p class="card-text"><small><em>Note: An entrance ticket purchase may be required.</em></small></p>
                            @endif
                             <p class="card-text">
                                <strong>Subscribers:</strong> {{ $tour->registrations->sum('num_participants') }} / {{ $tour->visitType->max_participants }}
                            </p>
                        </div>
                        <div class="card-footer text-center">
                            @if ($tour->status === \App\Models\Visit::STATUS_PROPOSED || $tour->status === \App\Models\Visit::STATUS_COMPLETE )
                                @auth
                                    @if (Auth::user()->hasRole('fruitore'))
                                        @if($tour->status === \App\Models\Visit::STATUS_PROPOSED)
                                            <a href="{{ route('visits.register.form', ['visit' => $tour->visit_id]) }}" class="btn btn-primary btn-sm">View Details</a>
                                        @else
                                            <a class="btn btn-primary btn-sm disabled">Max capacity reached</a>
                                        @endif
                                    @else
                                        <span class="text-muted">Registration available for Users only</span>
                                    @endif
                                @else
                                     @if($tour->status === \App\Models\Visit::STATUS_PROPOSED)
                                     <a href="{{ route('visits.register.form', ['visit' => $tour->visit_id]) }}" class="btn btn-primary btn-sm">View Details</a>
                                    @else
                                        <a class="btn btn-primary btn-sm disabled">Max capacity reached</a>
                                    @endif
                                @endauth
                            @else
                                <span class="text-muted">Registration Closed</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <hr>

    <h3>Confirmed Tours</h3>
    @if($confirmed_visits->isEmpty())
        <p>There are currently no confirmed tours scheduled.</p>
    @else
        <p>These tours are confirmed. You may view details:</p>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($confirmed_visits as $tour)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $tour->visitType->title }}</h5>
                            <h6 class="card-subtitle mb-2">
                                <span class="badge {{ $tour->status === 'proposed' ? 'bg-warning text-dark' : ($tour->status === 'confirmed' ? 'bg-success' : 'bg-secondary') }}">
                                    {{ ucfirst($tour->status)}}
                                </span>
                            </h6>
                            <p class="card-text">
                                <strong>Place:</strong> {{ $tour->visitType->place->name }}<br>
                                <small class="text-muted">{{ $tour->visitType->place->location }}</small>
                            </p>
                            <p class="card-text">
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($tour->visit_date)->format('D, M j, Y') }}<br>
                                <strong>Time:</strong> {{ \Carbon\Carbon::parse($tour->visitType->start_time)->format('g:i A') }} ({{ $tour->visitType->duration_minutes }} mins)<br>
                                <strong>Meeting:</strong> {{ $tour->visitType->meeting_point }}
                            </p>
                            <p class="card-text"><small>{!! nl2br(e($tour->visitType->description)) !!}</small></p>
                            @if ($tour->visitType->requires_ticket)
                                <p class="card-text"><small><em>Note: An entrance ticket purchase is required.</em></small></p>
                            @endif
                             <p class="card-text">
                                <strong>Subscribers:</strong> {{ $tour->registrations->sum('num_participants') }} / {{ $tour->visitType->max_participants }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(!session('error_message') && $proposed_visits->isEmpty() && $confirmed_visits->isEmpty())
        <hr>
        <p>There are currently no tours scheduled (neither proposed nor confirmed). Please check back later!</p>
    @endif

@endsection
