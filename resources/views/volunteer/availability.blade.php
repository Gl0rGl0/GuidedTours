@extends('layouts.app')

@section('title', 'Declare Availability')

@push('styles')
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
    <style>
        .calendar-header.clickable {
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .calendar-header.clickable:hover {
            background-color: var(--bs-primary-bg-subtle, #cfe2ff) !important;
            color: var(--bs-primary, #0d6efd) !important;
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold text-primary mb-2">Volunteer Availability</h2>
                        <p class="text-muted">Select the days you are available to guide tours for <strong>{{ $monthName }}</strong>.</p>
                    </div>

                    <form action="{{ route('volunteer.availability.store') }}" method="POST" id="availability-form">
                        @csrf
                        
                        <div class="d-flex justify-content-center">
                            <div class="calendar-wrapper shadow-sm rounded-3 overflow-hidden border mb-4" style="max-width: 800px; width: 100%;">
                                <div class="calendar-grid">
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="0" title="Select all Mondays">Mon</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="1" title="Select all Tuesdays">Tue</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="2" title="Select all Wednesdays">Wed</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="3" title="Select all Thursdays">Thu</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="4" title="Select all Fridays">Fri</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="5" title="Select all Saturdays">Sat</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="6" title="Select all Sundays">Sun</div>

                                    @php
                                        $firstDayOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth();
                                        $startDayOfWeek = $firstDayOfMonth->dayOfWeekIso;
                                        $offset = $startDayOfWeek - 1;
                                    @endphp

                                    @for ($i = 0; $i < $offset; $i++)
                                        <div class="calendar-day other-month bg-light opacity-50"></div>
                                    @endfor

                                    @for ($day = 1; $day <= $daysInMonth; $day++)
                                        @php
                                            $isSelected = isset($existingAvailability[$day]);
                                        @endphp
                                        <div class="calendar-day {{ $isSelected ? 'selected' : '' }}" data-day="{{ $day }}">
                                            <span class="day-number">{{ $day }}</span>
                                            <input class="form-check-input d-none"
                                                type="checkbox"
                                                name="available_days[]"
                                                value="{{ $day }}"
                                                id="day_{{ $day }}"
                                                {{ $isSelected ? 'checked' : '' }}>
                                            
                                            @if($isSelected)
                                                <div class="check-indicator"><i class="bi bi-check-circle-fill text-white"></i></div>
                                            @endif
                                        </div>
                                    @endfor

                                    @php
                                        $totalCells = $offset + $daysInMonth;
                                        $remainingCells = (7 - ($totalCells % 7)) % 7;
                                    @endphp
                                    @for ($i = 0; $i < $remainingCells; $i++)
                                        <div class="calendar-day other-month bg-light opacity-50"></div>
                                    @endfor

                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                             <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                                <i class="bi bi-save me-2"></i> Save Availability
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
                
                // Toggle visual indicator if we want to be fancy, or just rely on CSS class
                // Ideally CSS handles the .selected state appearance
            }
        }
        
        const headerCell = event.target.closest('.calendar-header.clickable');
        if (headerCell && headerCell.dataset.dayIndex !== undefined) {
            const dayIndex = parseInt(headerCell.dataset.dayIndex, 10);
            
            // The grid contains 7 headers first, then the day cells
            const children = Array.from(calendarGrid.children);
            const dayCells = children.slice(7);
            
            // Find valid days in this column
            const columnCells = dayCells.filter((cell, index) => {
                return (index % 7) === dayIndex && !cell.classList.contains('other-month');
            });
            
            if (columnCells.length > 0) {
                // If every valid cell is selected, deselect all. Otherwise, select all.
                const allSelected = columnCells.every(cell => cell.classList.contains('selected'));
                const targetState = !allSelected;
                
                columnCells.forEach(cell => {
                    const checkbox = cell.querySelector('input[type="checkbox"]');
                    if (checkbox && checkbox.checked !== targetState) {
                        checkbox.checked = targetState;
                        cell.classList.toggle('selected', targetState);
                    }
                });
            }
        }
    });

});
</script>
@endpush
