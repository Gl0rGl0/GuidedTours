@extends('layouts.app')

@section('title', 'Visit Planning')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">Visit Planning</h2>
            <p class="text-muted mb-0">Overview of schedules and volunteer availability</p>
        </div>
        <a href="{{ route('admin.visits.create') }}" class="btn btn-primary rounded-pill shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Add New Visit
        </a>
    </div>

    <!-- Date Range Info -->
    <div class="alert alert-light border shadow-sm d-flex align-items-center mb-5 rounded-4">
        <i class="bi bi-calendar-range text-primary fs-4 me-3"></i>
        <div>
            <small class="text-muted text-uppercase fw-bold">Planning Period</small>
            <div class="fw-bold">{{ $startDate->format('M j, Y') }} — {{ $endDate->format('M j, Y') }}</div>
        </div>
    </div>

    <!-- Planned Visits -->
    <div class="card shadow-sm border-0 rounded-4 mb-5">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
             <h4 class="fw-bold text-success mb-0"><i class="bi bi-geo-alt me-2"></i>Planned Visits</h4>
        </div>
        <div class="card-body p-4">
            @if ($plannedVisits->isNotEmpty())
                @php
                    // Organize by Month -> Week
                    $months = [];
                    foreach ($plannedVisits as $date => $visitsOnDate) {
                        $monthName = \Carbon\Carbon::parse($date)->format('F Y');
                        $day = (int) \Carbon\Carbon::parse($date)->day;
                        $weekIndex = $day <= 7 ? 0 : ($day <= 14 ? 1 : ($day <= 21 ? 2 : 3));
                        $months[$monthName][$weekIndex][$date] = $visitsOnDate;
                    }
                @endphp

                @foreach ($months as $month => $weeks)
                    <div class="mb-5 last:mb-0">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-success py-2 px-3 rounded-pill me-3">{{ $month }}</span>
                            <hr class="flex-grow-1 opacity-25">
                        </div>
                        <div class="row g-4">
                            @foreach ($weeks as $i => $week)
                                <div class="col-md-6 col-lg-3">
                                    <div class="bg-light p-3 rounded-3 h-100">
                                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Week {{ $i + 1 }}</h6>
                                        @if (!empty($week))
                                            @foreach ($week as $date => $visits)
                                                <div class="mb-3">
                                                    <div class="fw-bold text-dark border-bottom pb-1 mb-2">{{ \Carbon\Carbon::parse($date)->format('M d') }}</div>
                                                    <ul class="list-unstyled mb-0 small">
                                                        @foreach ($visits as $visit)
                                                            <li class="mb-2 p-2 bg-white rounded shadow-sm border-start border-4 border-success">
                                                                <div class="fw-bold">{{ $visit->start_time ? $visit->start_time->format('H:i') . ' - ' : '' }} {{ $visit->visitType->title }}</div>
                                                                <div class="text-muted">{{ $visit->visitType->place->name }}</div>
                                                                <div class="text-xs text-muted mt-1">
                                                                    <i class="bi bi-person-badge me-1"></i> {{ $visit->assignedVolunteer->username ?? 'Unassigned' }}
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted small fst-italic">No visits</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x display-4 mb-3 d-block opacity-25"></i>
                    No visits planned.
                </div>
            @endif
        </div>
    </div>

    <!-- Volunteer Availability -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
             <h4 class="fw-bold text-info mb-0"><i class="bi bi-people me-2"></i>Volunteer Availability</h4>
        </div>
        <div class="card-body p-4">
            @if ($volunteerAvailabilities->isNotEmpty())
                @php
                    $monthsAvail = [];
                    foreach ($volunteerAvailabilities as $date => $availOnDate) {
                        $monthName = \Carbon\Carbon::parse($date)->format('F Y');
                        $day = (int) \Carbon\Carbon::parse($date)->day;
                        $weekIndex = $day <= 7 ? 0 : ($day <= 14 ? 1 : ($day <= 21 ? 2 : 3));
                        $monthsAvail[$monthName][$weekIndex][$date] = $availOnDate;
                    }
                @endphp

                @foreach ($monthsAvail as $month => $weeks)
                    <div class="mb-5 last:mb-0">
                         <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-info text-dark py-2 px-3 rounded-pill me-3">{{ $month }}</span>
                            <hr class="flex-grow-1 opacity-25">
                        </div>
                        <div class="row g-4">
                            @foreach ($weeks as $i => $week)
                                <div class="col-md-6 col-lg-3">
                                     <div class="bg-light p-3 rounded-3 h-100">
                                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Week {{ $i + 1 }}</h6>
                                        @if (!empty($week))
                                            @foreach ($week as $date => $availabilities)
                                                 <div class="mb-3">
                                                    <div class="fw-bold text-dark border-bottom pb-1 mb-2">{{ \Carbon\Carbon::parse($date)->format('M d') }}</div>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach ($availabilities as $availability)
                                                            <span class="badge bg-white text-dark border shadow-sm fw-normal">
                                                                {{ $availability->volunteer->username }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted small fst-italic">No data</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-person-x display-4 mb-3 d-block opacity-25"></i>
                    No availability recorded.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
