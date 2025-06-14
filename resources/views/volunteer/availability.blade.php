@extends('layouts.app')

@section('title', 'Declare Availability')

@push('styles')
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <h2>Declare Availability for {{ $monthName }}</h2>
    <p>Click on the days you are available to volunteer next month.</p>

    {{-- Removed static alert boxes --}}

    <form action="{{ route('volunteer.availability.store') }}" method="POST" id="availability-form">
        @csrf
        <div class="calendar-grid mb-3">
            {{-- Calendar Headers (Mon-Sun) --}}
            <div class="calendar-header">Mon</div>
            <div class="calendar-header">Tue</div>
            <div class="calendar-header">Wed</div>
            <div class="calendar-header">Thu</div>
            <div class="calendar-header">Fri</div>
            <div class="calendar-header">Sat</div>
            <div class="calendar-header">Sun</div>

            @php
                $firstDayOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth();
                // dayOfWeekIso returns 1 (Mon) to 7 (Sun)
                $startDayOfWeek = $firstDayOfMonth->dayOfWeekIso;
                // Calculate offset needed for grid (0 for Mon, 1 for Tue, ..., 6 for Sun)
                $offset = $startDayOfWeek - 1;
            @endphp

            {{-- Empty cells for days before the 1st of the month --}}
            @for ($i = 0; $i < $offset; $i++)
                <div class="calendar-day other-month"></div>
            @endfor

            {{-- Days of the month --}}
            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $isSelected = isset($existingAvailability[$day]);
                @endphp
                <div class="calendar-day {{ $isSelected ? 'selected' : '' }}" data-day="{{ $day }}">
                    <span class="day-number">{{ $day }}</span>
                    {{-- Hidden checkbox --}}
                    <input class="form-check-input"
                           type="checkbox"
                           name="available_days[]"
                           value="{{ $day }}"
                           id="day_{{ $day }}"
                           {{ $isSelected ? 'checked' : '' }}>
                </div>
            @endfor

             {{-- Empty cells for days after the last of the month --}}
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
        // Find the closest calendar-day element that was clicked
        const dayCell = event.target.closest('.calendar-day:not(.other-month)');

        if (dayCell) {
            const checkbox = dayCell.querySelector('input[type="checkbox"]');
            if (checkbox) {
                // Toggle checkbox state
                checkbox.checked = !checkbox.checked;
                // Toggle visual class
                dayCell.classList.toggle('selected', checkbox.checked);
            }
        }
    });

});
</script>
@endpush
