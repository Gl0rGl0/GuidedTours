@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<div class="container">
    <h2>My Booked Tours</h2>

    @if ($bookings->isEmpty())
        <p>You have not booked any tours yet.</p>
    @else
        <p>Here are the tours you have booked:</p>
        <div class="list-group">
            @foreach ($bookings as $booking)
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $booking->visit->visitType->title }}</h5>
                        <small class="text-muted">Booking Code: {{ $booking->booking_code }}</small>
                    </div>
                    <p class="mb-1">
                        <strong>Place:</strong> {{ $booking->visit->visitType->place->name }}<br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->visit->visit_date)->format('D, M j, Y') }}<br>
                        <strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->visit->visitType->start_time)->format('g:i A') }}<br>
                        <strong>Participants:</strong> {{ $booking->num_participants }}
                    </p>
                    <div class="text-end">
                        <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel Booking</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
