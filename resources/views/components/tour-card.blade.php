@props([
    'visit', 
    'context' => 'home', // home, dashboard, archive
    'highlight' => false
])

<div class="card h-100 shadow-sm border-0 bg-white {{ $highlight ? 'highlight-card' : 'card-hover rounded-4' }}" id="{{ $context === 'dashboard' ? 'booking-'.$visit->visit_id : '' }}">
    <div class="card-body p-4 d-flex flex-column">
        {{-- Header: Location & Actions --}}
        <div class="d-flex justify-content-between align-items-start mb-3">
            @if($context === 'archive' || $context === 'dashboard')
                <x-visit-status-badge :status="$visit->status" :classes="$context === 'archive' ? 'text-uppercase' : ''" style="{{ $context === 'archive' ? 'font-size: 0.7rem;' : '' }}" />
            @else
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                    {{ $visit->visitType->place->name }}
                </span>
            @endif

            @if($context === 'dashboard')
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                    data-bs-toggle="modal" 
                    data-bs-target="#cancelModal" 
                    data-action="{{ route('user.bookings.cancel', $visit->registrations->where('user_id', Auth::id())->first() ?? 0) }}">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
            @elseif($context === 'archive')
                <small class="text-muted">{{ \Carbon\Carbon::parse($visit->visit_date)->format('M d, Y') }}</small>
            @endif
        </div>

        {{-- Title --}}
        <h5 class="card-title fw-bold mb-{{ $context === 'archive' ? '1' : '3' }}">{{ $visit->visitType->title }}</h5>

        {{-- Optional Description / Place (Archive Context) --}}
        @if($context === 'archive')
            <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i> {{ $visit->visitType->place->name }}</p>
        @endif

        {{-- Common Details --}}
        <ul class="list-unstyled text-muted small mb-4 flex-grow-1">
            @if($context !== 'archive')
                @if($context === 'home')
                    <li class="mb-2"><i class="bi bi-geo-alt me-1 text-primary"></i> 
                        {{ $visit->visitType->place->name }}
                    </li>
                @endif
                <li class="mb-2 d-flex align-items-center">
                    <i class="bi bi-calendar3 me-2 text-primary"></i> 
                    <span>{{ \Carbon\Carbon::parse($visit->visit_date)->format('D, M j, Y') }}</span>
                    @if($context === 'home' && $visit->is_imminent)
                        <span class="badge bg-primary-subtle text-primary rounded-pill ms-2">Imminent</span>
                    @endif
                </li>
            @endif

            <li class="mb-2">
                <i class="bi bi-clock me-2 {{ $context === 'archive' ? 'text-secondary' : 'text-primary' }}"></i>
                {{ \Carbon\Carbon::parse($visit->effective_start_time ?? $visit->visitType->start_time)->format('g:i A') }}
                @if($context === 'home')
                    ({{ $visit->visitType->duration_minutes }} min)
                @endif
            </li>

            {{-- Participants --}}
            <li class="mb-2 d-flex align-items-center">
                <i class="bi bi-people me-2 {{ $context === 'archive' ? 'text-secondary' : 'text-primary' }}"></i> 

                @if($context === 'dashboard')
                    @php 
                        $userReg = $visit->registrations->where('user_id', Auth::id())->first();
                    @endphp
                    {{ $userReg ? $userReg->num_participants : 0 }} Participants
                @elseif($context === 'archive')
                    {{ $visit->registrations->sum('num_participants') }} Attendees
                @else
                    <span>{{ $visit->registrations->sum('num_participants') }} / {{ $visit->visitType->max_participants }} Filled</span>
                    @if($visit->is_filling_fast)
                        <span class="badge bg-warning-subtle text-warning rounded-pill ms-2">{{ $visit->spots_remaining }} Spots Remaining</span>
                    @endif
                @endif
            </li>

            {{-- Meeting Point / Volunteer --}}
            @if($context === 'home' || $context === 'archive')
                <li class="mb-2"><i class="bi bi-map me-2 {{ $context === 'archive' ? 'text-secondary' : 'text-primary' }}"></i> {{ $visit->visitType->meeting_point }}</li>
            @endif

            @if($context === 'archive' && $visit->assignedVolunteer && $visit->assignedVolunteer->id !== Auth::id())
                <li class="mb-2"><i class="bi bi-person-badge me-2 text-secondary"></i> Vol: {{ $visit->assignedVolunteer->first_name }} {{ $visit->assignedVolunteer->last_name }}</li>
            @endif
        </ul>

        {{-- Description snippet (Home Context) --}}
        @if($context === 'home')
            <p class="card-text small text-muted line-clamp-3">
                {{ Str::limit($visit->visitType->description, 100) }}
            </p>
        @endif
    </div>

    {{-- Footer Actions --}}
    @if($context === 'home' || $context === 'dashboard')
        <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
            @if($context === 'home')
                @auth
                    @role('Customer')
                        @php
                            $isBooked = $visit->registrations->contains('user_id', Auth::id());
                        @endphp

                        @if($isBooked)
                            <a href="{{ route('user.dashboard') . '?highlight=' . $visit->visit_id }}"
                                class="btn btn-outline-primary w-100 rounded-pill stretched-link">
                                Already Booked
                            </a>
                        @elseif($visit->registrations->sum('num_participants') >= $visit->visitType->max_participants)
                            <button class="btn btn-secondary w-100 rounded-pill" disabled>
                                Sold Out
                            </button>
                        @else
                            <a href="{{ route('visits.register.form', ['visit' => $visit->visit_id]) }}"
                                class="btn btn-primary w-100 rounded-pill stretched-link">
                                View Details & Book
                            </a>
                        @endif
                    @else
                        <button class="btn btn-secondary w-100 rounded-pill" disabled>Customer Only</button>
                    @endrole
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 rounded-pill stretched-link">Login to Book</a>
                @endauth

            @elseif($context === 'dashboard')
                @php
                    $bookingCode = $visit->registrations->where('user_id', Auth::id())->first()?->booking_code;
                @endphp
                @if($bookingCode)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Booking Code</small>
                        <a href="{{ route('tickets.download', $bookingCode) }}" class="btn btn-sm btn-outline-dark rounded-pill shadow-sm" target="_blank" title="Download PDF Ticket">
                            <i class="bi bi-file-earmark-pdf me-1"></i> View Ticket
                        </a>
                    </div>
                    <div class="bg-light rounded p-2 text-center text-monospace fw-bold letter-spacing-1">
                        {{ $bookingCode }}
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
