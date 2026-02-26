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

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($visits as $visit)
            <div class="col">
                <x-tour-card :visit="$visit" context="archive" />
            </div>
        @empty
            <div class="col-12 text-center py-5 card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <x-empty-state 
                        icon="bi-clock-history" 
                        title="No records found" 
                        message="There are no visits to display in this category." 
                        actionText="Back to Home"
                        actionUrl="{{ route('home') }}"
                        :card="false" 
                    />
                </div>
            </div>
        @endforelse
    </div>
@endsection
