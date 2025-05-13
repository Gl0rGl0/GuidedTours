@extends('layouts.app')

@section('title', 'Visit Planning')

@section('content')
    <h2>Visit Planning</h2>
    <hr>

    <p>Planning period: {{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }}</p>

    <h3>Planned Visits</h3>

    @if ($plannedVisits->isNotEmpty())
        @php
            // Organizza le date in 4 settimane
            $weeks = [[], [], [], []];
            foreach ($plannedVisits as $date => $visitsOnDate) {
                $day = (int) \Carbon\Carbon::parse($date)->day;
                if ($day <= 7)      $weeks[0][$date] = $visitsOnDate;
                elseif ($day <= 14) $weeks[1][$date] = $visitsOnDate;
                elseif ($day <= 21) $weeks[2][$date] = $visitsOnDate;
                else                 $weeks[3][$date] = $visitsOnDate;
            }
        @endphp

        <div class="row">
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
    @else
        <p class="text-muted">No visits planned in this period.</p>
    @endif


    <h3 class="mt-4">Volunteer Availability</h3>

    @if ($volunteerAvailabilities->isNotEmpty())
        @php
            // Organizza le date in 4 “settimane”
            $weeks = [[], [], [], []];
            foreach ($volunteerAvailabilities as $date => $availabilitiesOnDate) {
                $day = (int) \Carbon\Carbon::parse($date)->day;
                if ($day <= 7)      $weeks[0][$date] = $availabilitiesOnDate;
                elseif ($day <= 14) $weeks[1][$date] = $availabilitiesOnDate;
                elseif ($day <= 21) $weeks[2][$date] = $availabilitiesOnDate;
                else                 $weeks[3][$date] = $availabilitiesOnDate;
            }
        @endphp

        <div class="row">
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
    @else
        <p class="text-muted">No volunteer availability recorded for this period.</p>
    @endif

@endsection