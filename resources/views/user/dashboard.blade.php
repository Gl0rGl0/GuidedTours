@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">My Bookings</h2>
            <p class="text-muted mb-0">Manage your upcoming and past tours</p>
        </div>
          @if (!$bookings->isEmpty())
        <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Book New Tour
        </a>
        @endif
    </div>

    <div class="row g-4">
        @forelse ($bookings as $booking)
            <div class="col-md-6 col-lg-4">
                <x-tour-card 
                    :visit="$booking->visit" 
                    context="dashboard" 
                    :highlight="request('highlight') == $booking->visit_id" 
                />
            </div>
        @empty
            <div class="col-12">
                <x-empty-state 
                    icon="bi-calendar2-x" 
                    title="No bookings found" 
                    message="You haven't booked any tours yet." 
                    actionText="Browse Tours"
                    actionUrl="{{ route('home') }}"
                    :card="false" 
                />
            </div>
        @endforelse
    </div>
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
