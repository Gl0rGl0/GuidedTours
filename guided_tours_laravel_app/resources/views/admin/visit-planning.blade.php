@extends('layouts.app')

@section('title', 'Visit Planning')

@section('content')
    <h2>Visit Planning</h2>
    <hr>

    <p>Planning period: {{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }}</p>

    <h3>Planned Visits</h3>
    @if ($plannedVisits->isNotEmpty())
        @foreach ($plannedVisits as $date => $visitsOnDate)
            <h4>{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</h4>
            <ul>
                @foreach ($visitsOnDate as $visit)
                    <li>
                        {{ $visit->start_time ? $visit->start_time->format('H:i') . ' - ' : '' }} {{ $visit->visitType->title }} at
                        {{ $visit->visitType->place->name }}
                        (Volunteer: {{ $visit->assignedVolunteer->username ?? 'Unassigned' }})
                    </li>
                @endforeach
            </ul>
        @endforeach
    @else
        <p>No visits planned in this period.</p>
    @endif

    <h3 class="mt-4">Volunteer Availability</h3>
    @if ($volunteerAvailabilities->isNotEmpty())
        @foreach ($volunteerAvailabilities as $date => $availabilitiesOnDate)
            <h4>{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</h4>
            <ul>
                @foreach ($availabilitiesOnDate as $availability)
                    <li>
                        {{ $availability->volunteer->username }}
                    </li>
                @endforeach
            </ul>
        @endforeach
    @else
        <p>No volunteer availability recorded for this period.</p>
    @endif
@endsection