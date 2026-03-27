@extends('layouts.app')

@section('title', __('messages.volunteer.availability.page_title'))

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
        #availability-title {
            scroll-margin-top: 110px;
        }
        /* Nuovi stili per una legenda più pulita */
        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        .legend-dot.available {
            background-color: var(--bs-primary, #0d6efd); 
        }
        .legend-dot.unavailable {
            background-color: #e9ecef; border: 1px solid #dee2e6;
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <h2 id="availability-title" class="fw-bold text-primary mb-2">
                            {{ __('messages.volunteer.availability.title') }}
                        </h2>
                        <p class="text-muted fs-5">
                            {!! __('messages.volunteer.availability.description', ['monthName' => $monthName]) !!}
                        </p>
                    </div>

                    <form action="{{ route('volunteer.availability.store') }}" method="POST" id="availability-form">
                        @csrf
                        
                        <div class="d-flex flex-column align-items-center">
                            
                            <div class="alert alert-light border-0 text-muted small py-2 px-3 mb-3 text-center rounded-pill" role="alert">
                                <i class="bi bi-info-circle me-1"></i> 
                                {{ __('messages.volunteer.availability.legend.column_select') }}
                            </div>

                            <div class="calendar-wrapper shadow-sm rounded-3 overflow-hidden border mb-4" style="max-width: 800px; width: 100%;">
                                <div class="calendar-grid">
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="0" title="{{ __('messages.volunteer.availability.mon_title') }}">{{ __('messages.volunteer.availability.days.mon') }}</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="1" title="{{ __('messages.volunteer.availability.tue_title') }}">{{ __('messages.volunteer.availability.days.tue') }}</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="2" title="{{ __('messages.volunteer.availability.wed_title') }}">{{ __('messages.volunteer.availability.days.wed') }}</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="3" title="{{ __('messages.volunteer.availability.thu_title') }}">{{ __('messages.volunteer.availability.days.thu') }}</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="4" title="{{ __('messages.volunteer.availability.fri_title') }}">{{ __('messages.volunteer.availability.days.fri') }}</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="5" title="{{ __('messages.volunteer.availability.sat_title') }}">{{ __('messages.volunteer.availability.days.sat') }}</div>
                                    <div class="calendar-header clickable bg-light py-2 fw-bold text-secondary" data-day-index="6" title="{{ __('messages.volunteer.availability.sun_title') }}">{{ __('messages.volunteer.availability.days.sun') }}</div>

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
                                            <input class="form-check-input d-none" type="checkbox" name="available_days[]" value="{{ $day }}" id="day_{{ $day }}" {{ $isSelected ? 'checked' : '' }}>
                                            
                                            @if(!$isSelected)
                                                <div class="check-indicator">*</div>
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

                            <div class="d-flex justify-content-center gap-4 mb-4">
                                <div class="d-flex align-items-center">
                                    <span class="legend-dot available me-2"></span>
                                    <span class="small text-muted">{{ __('messages.volunteer.availability.legend.available') }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="legend-dot unavailable me-2"></span>
                                    <span class="small text-muted">{{ __('messages.volunteer.availability.legend.unavailable') }}</span>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-between align-items-center border-top pt-4 mt-2">
                            <div class="text-danger small fw-medium">
                                <i class="bi bi-asterisk me-1"></i> {{ __('messages.volunteer.availability.legend.unsaved_changes') }}
                            </div>
                            
                            <div class="d-flex">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4 me-3">{{ __('messages.volunteer.availability.cancel_btn') }}</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-save me-2"></i> {{ __('messages.volunteer.availability.save_btn') }}
                                </button>
                            </div>
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

    // Smooth scroll to title on entry
    setTimeout(() => {
        const title = document.getElementById('availability-title');
        if (title) {
            title.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 500); 
});
</script>
@endpush