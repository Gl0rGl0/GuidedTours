@extends('layouts.app')

@section('title', 'Visit Planning')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="mb-0">Visit Planning</h2>
        <a href="{{ route('admin.visits.create') }}" class="btn btn-primary">+ Add New Visit</a>
    </div>
    <hr>


    <p>Planning period: {{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }}</p>

    <h3>Planned Visits</h3>

    @if ($plannedVisits->isNotEmpty())
        @php
            // Organizza per mese e poi in 4 settimane ciascuno
            $months = [];
            foreach ($plannedVisits as $date => $visitsOnDate) {
                $monthName = \Carbon\Carbon::parse($date)->format('F Y');
                $day = (int) \Carbon\Carbon::parse($date)->day;
                $weekIndex = $day <= 7 ? 0 : ($day <= 14 ? 1 : ($day <= 21 ? 2 : 3));
                $months[$monthName][$weekIndex][$date] = $visitsOnDate;
            }
        @endphp

        @foreach ($months as $month => $weeks)
            <div class="month-section mb-4">
                <div class="d-flex align-items-center">
                    <hr class="flex-grow-1 me-2" />
                    <span class="text-muted fw-bold">{{ $month }}</span>
                    <hr class="flex-grow-1 ms-2" />
                </div>
                <div class="row mt-2">
                    @foreach ($weeks as $i => $week)
                        <div class="col-6 col-md-3 mb-4">
                            <h5>Week {{ $i + 1 }}</h5>

                            @if (!empty($week))
                                @foreach ($week as $date => $visits)
                                    <div class="mb-2">
                                        <strong>{{ \Carbon\Carbon::parse($date)->format('M d') }}</strong>
                                        <ul class="list-unstyled ps-3 mb-0">
                                            @foreach ($visits as $visit)
                                                <li>
                                                    {{ $visit->start_time ? $visit->start_time->format('H:i') . ' - ' : '' }}
                                                    {{ $visit->visitType->title }} at {{ $visit->visitType->place->name }}
                                                    (Volunteer: {{ $visit->assignedVolunteer->username ?? 'Unassigned' }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No entries</p>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <p class="text-muted">No visits planned in this period.</p>
    @endif


    <h3 class="mt-4">Volunteer Availability</h3>

    @if ($volunteerAvailabilities->isNotEmpty())
        @php
            // Organizza per mese e poi in 4 settimane ciascuno
            $monthsAvail = [];
            foreach ($volunteerAvailabilities as $date => $availOnDate) {
                $monthName = \Carbon\Carbon::parse($date)->format('F Y');
                $day = (int) \Carbon\Carbon::parse($date)->day;
                $weekIndex = $day <= 7 ? 0 : ($day <= 14 ? 1 : ($day <= 21 ? 2 : 3));
                $monthsAvail[$monthName][$weekIndex][$date] = $availOnDate;
            }
        @endphp

        @foreach ($monthsAvail as $month => $weeks)
            <div class="month-section mb-4">
                <div class="d-flex align-items-center">
                    <hr class="flex-grow-1 me-2" />
                    <span class="text-muted fw-bold">{{ $month }}</span>
                    <hr class="flex-grow-1 ms-2" />
                </div>
                <div class="row mt-2">
                    @foreach ($weeks as $i => $week)
                        <div class="col-6 col-md-3 mb-4">
                            <h5>Week {{ $i + 1 }}</h5>

                            @if (!empty($week))
                                @foreach ($week as $date => $availabilities)
                                    <div class="mb-2">
                                        <strong>{{ \Carbon\Carbon::parse($date)->format('M d') }}</strong>
                                        <ul class="list-unstyled ps-3 mb-0">
                                            @foreach ($availabilities as $availability)
                                                <li>{{ $availability->volunteer->username }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No entries</p>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <p class="text-muted">No volunteer availability recorded for this period.</p>
    @endif

@endsection
