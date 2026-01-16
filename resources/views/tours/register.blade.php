@extends('layouts.app')

@section('title', 'Register for Tour')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 text-center">
                    <h4 class="fw-bold text-primary mb-1">Confirm Registration</h4>
                    <p class="text-muted small">Review the details before booking</p>
                </div>
                <div class="card-body p-4">
                    @if ($visit)
                        <div class="bg-light rounded-3 p-4 mb-4">
                            <h5 class="fw-bold text-dark mb-3">{{ $visit->visitType->title }}</h5>
                            
                            <ul class="list-unstyled text-muted small mb-0">
                                <li class="mb-2 d-flex">
                                    <i class="bi bi-geo-alt me-2 text-primary flex-shrink-0"></i>
                                    <div>
                                        <strong>{{ $visit->visitType->place->name }}</strong>
                                        <div class="text-muted">{{ $visit->visitType->place->location }}</div>
                                    </div>
                                </li>
                                <li class="mb-2 d-flex pt-2 border-top">
                                     <i class="bi bi-calendar3 me-2 text-primary"></i>
                                     {{ \Carbon\Carbon::parse($visit->visit_date)->format('D, M j, Y') }}
                                </li>
                                <li class="mb-2 d-flex">
                                     <i class="bi bi-clock me-2 text-primary"></i>
                                     {{ \Carbon\Carbon::parse($visit->visitType->start_time)->format('g:i A') }} ({{ $visit->visitType->duration_minutes }} mins)
                                </li>
                                <li class="mb-2 d-flex">
                                     <i class="bi bi-people me-2 text-primary"></i>
                                     {{ $visit->registrations->sum('num_participants') }} / {{ $visit->visitType->max_participants }} Participants
                                </li>
                            </ul>

                             @if ($visit->visitType->requires_ticket)
                                <div class="alert alert-warning d-flex align-items-center mt-3 mb-0 py-2 small">
                                    <i class="bi bi-ticket-perforated me-2"></i>
                                    <div>Remember: Entrance ticket required at venue.</div>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('visits.register.submit', ['visit' => $visit->visit_id]) }}" method="POST">
                            @csrf
                            <div class="form-floating mb-4">
                                <input type="number" id="num_participants" name="num_participants" class="form-control" placeholder="Participants" min="1" value="1" required>
                                <label for="num_participants">Number of Participants</label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                    Confirm Booking
                                </button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill">Cancel</a>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-exclamation-circle text-muted display-1 mb-3"></i>
                            <p class="lead text-muted">Tour details not found.</p>
                            <a href="{{ route('home') }}" class="btn btn-primary rounded-pill">Back to Home</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
