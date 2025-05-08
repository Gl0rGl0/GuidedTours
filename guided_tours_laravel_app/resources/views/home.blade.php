@extends('layouts.app') {{-- Extend the main layout --}}

@section('title', 'Home - Guided Tours') {{-- Set the page title --}}

@section('content')
    <h2>Welcome to the Guided Tours Portal</h2>
    <p>Discover amazing places and experiences offered by our dedicated volunteers.</p>
    <p>Explore the available tours below or use the navigation above to log in and manage your activities.</p>

    <hr> <!-- Separator -->

    @if(session('error_message'))
        <p style="color: red;">{{ session('error_message') }}</p>
    @endif

    <h3>Proposed Tours</h3>
    {{-- Passed only COMPLETE or PROPOSED visits by controller --}}
    @if($proposed_visits->isEmpty())
        <p>There are currently no proposed tours scheduled. Please check back later!</p>
    @else
        <p>Here are the upcoming guided tours open for registration:</p>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"> {{-- Bootstrap grid --}}
            @foreach ($proposed_visits as $tour)
                <div class="col">
                    <div class="card h-100"> {{-- Bootstrap card --}}
                        <div class="card-body">
                            <h5 class="card-title">{{ $tour->visitType->title }}</h5> {{-- Access title through visitType relationship --}}
                            <h6 class="card-subtitle mb-2">
                                <span class="badge {{ $tour->status === 'proposed' ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ ucfirst($tour->status)}}
                                </span>
                            </h6>
                            <p class="card-text">
                                <strong>Place:</strong> {{ $tour->visitType->place->name }}<br> {{-- Access place name through visitType and place relationships --}}
                                <small class="text-muted">{{ $tour->visitType->place->location }}</small> {{-- Access place location through visitType and place relationships --}}
                            </p>
                            <p class="card-text">
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($tour->visit_date)->format('D, M j, Y') }}<br>
                                <strong>Time:</strong> {{ \Carbon\Carbon::parse($tour->visitType->start_time)->format('g:i A') }} ({{ $tour->visitType->duration_minutes }} mins)<br> {{-- Access time and duration through visitType relationship --}}
                                <strong>Meeting:</strong> {{ $tour->visitType->meeting_point }} {{-- Access meeting point through visitType relationship --}}
                            </p>
                            <p class="card-text"><small>{!! nl2br(e($tour->visitType->description)) !!}</small></p> {{-- Access description through visitType relationship --}}
                            @if ($tour->visitType->requires_ticket)
                                <p class="card-text"><small><em>Note: An entrance ticket purchase may be required.</em></small></p>
                            @endif
                             <p class="card-text">
                                <strong>Subscribers:</strong> {{ $tour->registrations->sum('num_participants') }} / {{ $tour->visitType->max_participants }} {{-- Calculate and display subscriber count --}}
                            </p>
                        </div>
                        <div class="card-footer text-center">
                             {{-- Registration link logic --}}
                             {{-- Show button if status is 'proposed' or 'complete'. Controller will handle actual eligibility. --}}
                            @if ($tour->status === \App\Models\Visit::STATUS_PROPOSED || $tour->status === \App\Models\Visit::STATUS_COMPLETE )
                                @auth
                                    @if (Auth::user()->hasRole('fruitore'))
                                        @if($tour->status === \App\Models\Visit::STATUS_PROPOSED)
                                            <a href="{{ route('register-tour.form', ['visit_id' => $tour->visit_id]) }}" class="btn btn-primary btn-sm">View Details</a>
                                        @else
                                            <a class="btn btn-primary btn-sm disabled">Max capacity reached</a>
                                        @endif
                                    @else
                                        <span class="text-muted">Registration available for Users only</span>
                                    @endif
                                @else
                                     {{-- Show link for guests, they will be prompted to login/register --}}
                                     <a href="{{ route('register-tour.form', ['visit_id' => $tour->visit_id]) }}" class="btn btn-primary btn-sm">
                                        {{ $tour->status === \App\Models\Visit::STATUS_PROPOSED ? 'Register Interest' : 'View Details / Register' }}
                                     </a>
                                @endauth
                            @else
                                <span class="text-muted">Registration Closed</span> {{-- Fallback for other statuses if any --}}
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <hr> <!-- Separator -->

    <h3>Confirmed Tours</h3>
    @if($confirmed_visits->isEmpty())
        <p>There are currently no confirmed tours scheduled.</p>
    @else
        <p>These tours are confirmed and full. You may view details:</p>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"> {{-- Bootstrap grid --}}
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

    {{-- Overall message if both are empty and no error --}}
    @if(!session('error_message') && $proposed_visits->isEmpty() && $confirmed_visits->isEmpty())
        <hr>
        <p>There are currently no tours scheduled (neither proposed nor confirmed). Please check back later!</p>
    @endif

@endsection
