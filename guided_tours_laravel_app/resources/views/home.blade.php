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
        {{-- Basic styling for the tour list - consider moving to style.css --}}
        <style>
            .tour-list { list-style: none; padding: 0; }
            .tour-item { background: #f9f9f9; border: 1px solid #ddd; margin-bottom: 15px; padding: 15px; border-radius: 5px; }
            .tour-item h4 { margin-top: 0; color: #0779e4; } /* Changed to h4 for hierarchy */
            .tour-item strong { color: #333; }
            .tour-status { font-weight: bold; padding: 3px 8px; border-radius: 3px; color: #fff; display: inline-block; margin-bottom: 5px;}
            .status-proposed { background-color: #ffc107; color: #333; } /* Yellow */
            .status-confirmed { background-color: #28a745; } /* Green */
        </style>
        <ul class="tour-list">
            @foreach ($available_tours as $tour)
                <li class="tour-item">
                    <h4>{{ $tour->visit_type_title }}</h4>
                     <span class="tour-status status-{{ $tour->status }}">
                         {{ ucfirst($tour->status) }}
                     </span>
                    <p><strong>Place:</strong> {{ $tour->place_name }} ({{ $tour->place_location }})</p>
                    {{-- Use Carbon for date/time formatting --}}
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($tour->visit_date)->format('D, M j, Y') }}</p>
                    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($tour->start_time)->format('g:i A') }} (Duration: {{ $tour->duration_minutes }} mins)</p>
                    <p><strong>Meeting Point:</strong> {{ $tour->meeting_point }}</p>
                    <p>{!! nl2br(e($tour->visit_type_description)) !!}</p> {{-- Use e() for escaping and nl2br --}}
                    @if ($tour->requires_ticket)
                        <p><em>Note: An entrance ticket purchase may be required at the venue.</em></p>
                    @endif
                    {{-- Registration link logic (using placeholder route for now) --}}
                    @if ($tour->status === 'proposed')
                         <p><a href="{{ route('register-tour.form', ['visit_id' => $tour->visit_id]) }}">Register Interest</a></p>
                         {{-- TODO: Implement registration page/logic --}}
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

@endsection
