@extends('layouts.app')

@section('title', 'Declare Availability')

@push('styles')
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <h2>Declare Availability for {{ $monthName }}</h2>
    <p>Click on the days you are available to volunteer next month.</p>


    <form action="{{ route('volunteer.availability.store') }}" method="POST" id="availability-form">
        @csrf
        <div class="calendar-grid mb-3">
            <div class="calendar-header">Mon</div>
            <div class="calendar-header">Tue</div>
            <div class="calendar-header">Wed</div>
            <div class="calendar-header">Thu</div>
            <div class="calendar-header">Fri</div>
            <div class="calendar-header">Sat</div>
            <div class="calendar-header">Sun</div>

            @php
                $firstDayOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth();
                $startDayOfWeek = $firstDayOfMonth->dayOfWeekIso;
                $offset = $startDayOfWeek - 1;
            @endphp

            @for ($i = 0; $i < $offset; $i++)
                <div class="calendar-day other-month"></div>
            @endfor

            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $isSelected = isset($existingAvailability[$day]);
                @endphp
                <div class="calendar-day {{ $isSelected ? 'selected' : '' }}" data-day="{{ $day }}">
                    <span class="day-number">{{ $day }}</span>
                    <input class="form-check-input"
                           type="checkbox"
                           name="available_days[]"
                           value="{{ $day }}"
                           id="day_{{ $day }}"
                           {{ $isSelected ? 'checked' : '' }}>
                </div>
            @endfor

             @php
                $totalCells = $offset + $daysInMonth;
                $remainingCells = (7 - ($totalCells % 7)) % 7;
             @endphp
             @for ($i = 0; $i < $remainingCells; $i++)
                <div class="calendar-day other-month"></div>
            @endfor

        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Update Availability</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarGrid = document.querySelector('.calendar-grid');

    calendarGrid.addEventListener('click', function(event) {
        const dayCell = event.target.closest('.calendar-day:not(.other-month)');

        if (dayCell) {
            const checkbox = dayCell.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                dayCell.classList.toggle('selected', checkbox.checked);
            }
        }
    });

});
</script>
@endpush
