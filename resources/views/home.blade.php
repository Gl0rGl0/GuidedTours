@extends('layouts.app')

@section('title', 'Home - Guided Tours')

@section('content')

<!-- Hero Section (Full Width with Background) -->
<div class="hero-section bg-white border-bottom shadow-sm mb-5">
    <div class="container py-5">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 fw-bold mb-3 text-primary">Discover the Secret <br><span class="text-secondary">Beauty of the City</span></h1>
                <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                    Join our guided tours to explore the history, secret gardens, architecture, and hidden gems of the city. 
                    Book your visit today.
                </p>
                
                @auth
                    @if(Auth::user()->hasRole('Customer'))
                         <div class="d-flex justify-content-center gap-3">
                            <a href="#tours-section" class="btn btn-primary shadow-sm px-4 rounded-pill">Browse Tours</a>
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary px-4 rounded-pill">My Dashboard</a>
                        </div>
                    @else
                         <div class="d-flex justify-content-center gap-3">
                            <a href="#tours-section" class="btn btn-primary shadow-sm px-4 rounded-pill">Browse Tours</a>
                        </div>
                    @endif
                @else
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('login') }}" class="btn btn-primary shadow-sm px-4 rounded-pill">Login to Book</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary px-4 rounded-pill">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>

<div class="container pb-5" id="tours-section">

    @if(session('error_message'))
        <div class="alert alert-danger rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error_message') }}
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="card shadow-sm border-0 mb-5 p-2 rounded-4 bg-white" x-data>
        <form action="{{ route('home') }}" method="GET" x-ref="filterForm" class="row g-2 align-items-center">
            
            <!-- Search -->
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none" placeholder="Search tours..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="vr d-none d-md-block p-0 my-2" style="height: 20px; opacity: 0.2"></div>

            <!-- Location Filter -->
            <div class="col-md-2">
                <select name="place" class="form-select border-0 bg-transparent shadow-none text-muted" @change="$refs.filterForm.submit()">
                    <option value="">All Locations</option>
                    @foreach($places as $place)
                        <option value="{{ $place->place_id }}" {{ request('place') == $place->place_id ? 'selected' : '' }}>{{ $place->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sorting -->
            <div class="col-md-2">
                 <select name="sort" class="form-select border-0 bg-transparent shadow-none text-muted" @change="$refs.filterForm.submit()">
                    <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date: Soonest</option>
                    <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date: Latest</option>
                    <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                    <option value="alpha_asc" {{ request('sort') == 'alpha_asc' ? 'selected' : '' }}>Alphabetical</option>
                </select>
            </div>
            
            <!-- Price Toggle -->
            <div class="col-md-auto ms-auto d-flex align-items-center bg-light rounded-pill px-3 py-1 me-2">
                 <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" name="price" value="free" id="priceCheck" {{ request('price') == 'free' ? 'checked' : '' }} @change="$refs.filterForm.submit()">
                    <label class="form-check-label small fw-bold text-muted" for="priceCheck">Free Only</label>
                </div>
            </div>

            <!-- Submit (Hidden but accessible for Enter key on search) -->
            <button type="submit" class="d-none">Filter</button>
        </form>
    </div>

    <!-- Proposed Tours -->
    <div class="section-container mb-5">
        <div class="d-flex align-items-center mb-4 border-bottom pb-2">
            <h3 class="fw-bold text-primary mb-0 me-3">Upcoming Tours</h3>
            <span class="badge bg-primary-subtle text-primary rounded-pill">Open</span>
        </div>

        @if($proposed_visits->isEmpty())
            <div class="text-center py-5 card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-calendar-x display-4 text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted">No upcoming tours scheduled</h5>
                    <p class="text-muted small mb-0">Please check back later for new dates.</p>
                </div>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($proposed_visits as $tour)
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 card-hover bg-white">
                            <div class="card-body p-4">
                                 <div class="d-flex justify-content-between mb-3">
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">Open</span>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $tour->visitType->place->name }}</small>
                                </div>
                                
                                <h5 class="card-title fw-bold mb-3">{{ $tour->visitType->title }}</h5>
                                
                                 <ul class="list-unstyled text-muted small mb-4">
                                    <li class="mb-2"><i class="bi bi-calendar3 me-2 text-primary"></i> {{ \Carbon\Carbon::parse($tour->visit_date)->format('D, M j, Y') }}</li>
                                    <li class="mb-2"><i class="bi bi-clock me-2 text-primary"></i> {{ \Carbon\Carbon::parse($tour->visitType->start_time)->format('g:i A') }} ({{ $tour->visitType->duration_minutes }} min)</li>
                                    <li class="mb-2"><i class="bi bi-people me-2 text-primary"></i> {{ $tour->registrations->sum('num_participants') }} / {{ $tour->visitType->max_participants }} Filled</li>
                                    <li><i class="bi bi-map me-2 text-primary"></i> {{ $tour->visitType->meeting_point }}</li>
                                </ul>
                                
                                <p class="card-text small text-muted line-clamp-3">
                                    {{ Str::limit($tour->visitType->description, 100) }}
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                                @auth
                                    @if (Auth::user()->hasRole('Customer'))
                                        <a href="{{ route('visits.register.form', ['visit' => $tour->visit_id]) }}" class="btn btn-primary w-100 rounded-pill">
                                            View Details & Book
                                        </a>
                                    @else
                                         <button class="btn btn-secondary w-100 rounded-pill" disabled>Customer Only</button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 rounded-pill">Login to Book</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $proposed_visits->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- Confirmed Tours -->
    @if($confirmed_visits->isNotEmpty())
        <div class="section-container">
            <div class="d-flex align-items-center mb-4 border-bottom pb-2">
                <h3 class="fw-bold text-success mb-0 me-3">Your Confirmed Tours</h3>
                 <span class="badge bg-success-subtle text-success rounded-pill">Participating</span>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($confirmed_visits as $tour)
                     <div class="col">
                        <div class="card h-100 shadow-sm border-0 bg-white opacity-75">
                            <div class="card-body p-4">
                                 <div class="d-flex justify-content-between mb-3">
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Confirmed</span>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $tour->visitType->place->name }}</small>
                                </div>
                                <h5 class="card-title fw-bold mb-3">{{ $tour->visitType->title }}</h5>
                                <p class="text-muted small mb-0">
                                    This tour is confirmed. <br>
                                    Date: {{ \Carbon\Carbon::parse($tour->visit_date)->format('D, M j, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
