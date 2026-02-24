@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">My Bookings</h2>
            <p class="text-muted mb-0">Manage your upcoming and past tours</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Book New Tour
        </a>
    </div>

    @if ($bookings->isEmpty())
        <div class="text-center py-5">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="No bookings" style="max-height: 200px; opacity: 0.5;">
            <h4 class="mt-4 text-muted">No bookings found</h4>
            <p class="text-muted">You haven't booked any tours yet.</p>
            <a href="{{ route('home') }}" class="btn btn-primary rounded-pill mt-2">Browse Tours</a>
        </div>
    @else
        <div class="row g-4">
            @foreach ($bookings as $booking)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 {{ request('highlight') == $booking->visit_id ? 'highlight-card' : '' }}" id="booking-{{ $booking->visit_id }}">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                    {{ $booking->visit->visitType->title }}
                                </span>
                                <!-- Cancel action extracted from dropdown for better affordance (Issue 24) -->
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#cancelModal" 
                                    data-action="{{ route('user.bookings.cancel', $booking) }}">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </button>
                            </div>
                            
                            <h5 class="card-title fw-bold mb-3">{{ $booking->visit->visitType->place->name }}</h5>
                            
                            <ul class="list-unstyled text-muted small mb-0">
                                <li class="mb-2 d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-2 text-primary"></i> 
                                    {{ \Carbon\Carbon::parse($booking->visit->visit_date)->format('D, M j, Y') }}
                                </li>
                                <li class="mb-2 d-flex align-items-center">
                                    <i class="bi bi-clock me-2 text-primary"></i> 
                                    {{ \Carbon\Carbon::parse($booking->visit->effective_start_time ?? $booking->visit->visitType->start_time)->format('g:i A') }}
                                </li>
                                <li class="d-flex align-items-center">
                                    <i class="bi bi-people me-2 text-primary"></i> 
                                    {{ $booking->num_participants }} Participants
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                            <!-- Added Status Badge for clear visibility (Issue 5) -->
                            <div class="mb-3">
                                @if($booking->visit->status === \App\Models\Visit::STATUS_CONFIRMED)
                                    <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i> Confirmed</span>
                                @elseif($booking->visit->status === \App\Models\Visit::STATUS_COMPLETE)
                                    <span class="badge bg-secondary rounded-pill px-3 py-2"><i class="bi bi-flag me-1"></i> Completed</span>
                                @else
                                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-2"><i class="bi bi-calendar me-1"></i> Planned</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Booking Code</small>
                                <a href="{{ route('tickets.download', $booking->booking_code) }}" class="btn btn-sm btn-outline-dark rounded-pill shadow-sm" target="_blank" title="Download PDF Ticket">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View Ticket
                                </a>
                            </div>
                            <div class="bg-light rounded p-2 text-center text-monospace fw-bold letter-spacing-1">
                                {{ $booking->booking_code }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg p-3 rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="cancelModalLabel">Cancel Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-0">Are you sure you want to cancel this booking? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Keep Booking</button>
                <form id="cancelForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Yes, Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    @keyframes highlight-pulse {
        0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); transform: scale(1); }
        50% { box-shadow: 0 0 0 15px rgba(13, 110, 253, 0); transform: scale(1.02); }
        100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); transform: scale(1); }
    }
    .highlight-card {
        animation: highlight-pulse 2s ease-out;
        border-color: #0d6efd !important;
        background-color: #f8f9fa;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var cancelModal = document.getElementById('cancelModal');
        cancelModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var actionUrl = button.getAttribute('data-action');
            var form = document.getElementById('cancelForm');
            form.action = actionUrl;
        });

        // Scroll to highlighted booking
        const highlighted = document.querySelector('.highlight-card');
        if (highlighted) {
            highlighted.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
@endpush
@endsection
