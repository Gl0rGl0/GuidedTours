@extends('layouts.app') {{-- Extend the main layout --}}

@section('title', 'Home - Guided Tours') {{-- Set the page title --}}

@section('content')
    <h2>Welcome to the Guided Tours Portal</h2>
    <p>Discover amazing places and experiences offered by our dedicated volunteers.</p>
    <p>Explore the available tours below or use the navigation above to log in and manage your activities.</p>

    <hr> <!-- Separator -->

    <h3>Available Tours</h3>

    @if(session('error_message')) {{-- Check for error message passed from controller --}}
        <p style="color: red;">{{ session('error_message') }}</p>
    @elseif($available_tours->isEmpty()) {{-- Check if the collection is empty --}}
        <p>There are currently no available tours scheduled. Please check back later!</p>
    @else
        <p>Here are the upcoming guided tours available for registration or viewing:</p>
        {{-- Remove inline style block --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"> {{-- Bootstrap grid --}}
            @foreach ($available_tours as $tour)
                <div class="col">
                    <div class="card h-100"> {{-- Bootstrap card --}}
                        <div class="card-body">
                            <h5 class="card-title">{{ $tour->visitType->title }}</h5> {{-- Access title through visitType relationship --}}
                            <h6 class="card-subtitle mb-2">
                                <span class="badge {{ $tour->status === 'proposed' ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ ucfirst($tour->status) }}
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
                            @if ($tour->visitType->requires_ticket) {{-- Access requires_ticket through visitType relationship --}}
                                <p class="card-text"><small><em>Note: An entrance ticket purchase may be required.</em></small></p>
                            @endif
                             <p class="card-text">
                                <strong>Subscribers:</strong> {{ $tour->registrations->sum('num_participants') }} / {{ $tour->visitType->max_participants }} {{-- Calculate and display subscriber count --}}
                            </p>
                        </div>
                        <div class="card-footer text-center">
                             {{-- Registration link logic (using placeholder route for now) --}}
                            @if ($tour->status === 'proposed')
                                {{-- Only show registration link for 'fruitore' users --}}
                                @auth
                                    @if (Auth::user()->hasRole('fruitore'))
                                        <a href="{{ route('register-tour.form', ['visit_id' => $tour->visit_id]) }}" class="btn btn-primary btn-sm">Register Interest</a>
                                        {{-- TODO: Implement registration page/logic --}}
                                    @else
                                        <span class="text-muted">Registration available for Users only</span>
                                    @endif
                                @else
                                     {{-- Show link for guests, they will be prompted to login/register --}}
                                     <a href="{{ route('register-tour.form', ['visit_id' => $tour->visit_id]) }}" class="btn btn-primary btn-sm">Register Interest</a>
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

@endsection
