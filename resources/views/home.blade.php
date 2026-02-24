@extends('layouts.app')

@section('title', 'Home - Guided Tours')

@section('full-width-content')

    <!-- Hero Section (Full Width with Background) -->
    <div class="hero-section bg-white border-bottom shadow-sm mb-5">
        <div class="container py-5">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-4 fw-bold mb-3 text-primary">Discover the Secret <br><span
                            class="text-secondary">Beauty of the City</span></h1>
                    <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                        Join our guided tours to explore the history, secret gardens, architecture, and hidden gems of the
                        city.
                        Book your visit today.
                    </p>

                    @auth
                        @if(Auth::user()->hasRole('Customer'))
                            <div class="d-flex justify-content-center gap-3">
                                <a href="#tours-section" class="btn btn-primary shadow-sm px-4 rounded-pill">Browse Tours</a>
                                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary px-4 rounded-pill">My
                                    Dashboard</a>
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

    <div class="container pb-5" id="tours-section"
         x-data="{ 
             loading: false,
             updateTours() {
                 this.loading = true;
                 const formData = new FormData(this.$refs.filterForm);
                 const qs = new URLSearchParams(formData).toString();
                 const url = '{{ route('home') }}?' + qs;
                 
                 fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                     .then(r => r.text())
                     .then(html => {
                         const doc = new DOMParser().parseFromString(html, 'text/html');
                         const newWrapper = doc.getElementById('tours-list-wrapper');
                         if(newWrapper) {
                             document.getElementById('tours-list-wrapper').innerHTML = newWrapper.innerHTML;
                         }
                         window.history.pushState({}, '', url);
                         this.loading = false;
                     });
             },
             init() {
                 // Intercept pagination clicks
                 document.body.addEventListener('click', (e) => {
                     const link = e.target.closest('#tours-list-wrapper .pagination a');
                     if (link) {
                         e.preventDefault();
                         this.loading = true;
                         fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                             .then(r => r.text())
                             .then(html => {
                                 const doc = new DOMParser().parseFromString(html, 'text/html');
                                 const newWrapper = doc.getElementById('tours-list-wrapper');
                                 if(newWrapper) {
                                     document.getElementById('tours-list-wrapper').innerHTML = newWrapper.innerHTML;
                                 }
                                 window.history.pushState({}, '', link.href);
                                 
                                 // Sync form inputs with url params
                                 const params = new URL(link.href).searchParams;
                                 if (this.$refs.filterForm.search) this.$refs.filterForm.search.value = params.get('search') || '';
                                 if (this.$refs.filterForm.place) this.$refs.filterForm.place.value = params.get('place') || '';
                                 if (this.$refs.filterForm.sort) this.$refs.filterForm.sort.value = params.get('sort') || 'date_asc';
                                 
                                 const priceCheck = document.getElementById('priceCheck');
                                 if (priceCheck) priceCheck.checked = params.get('price') === 'free';

                                 this.loading = false;
                                 
                                 // scroll slightly up to see changes smoothly if pagination is at bottom
                                 document.getElementById('tours-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
                             });
                     }
                 });
             }
         }">

        @if(session('error_message'))
            <div class="alert alert-danger rounded-4 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error_message') }}
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="card shadow-sm border-0 mb-5 py-3 px-4 rounded-4 bg-light">
            <form action="{{ route('home') }}" method="GET" x-ref="filterForm" @submit.prevent="updateTours" class="row g-2 align-items-center m-0">

                <!-- Search -->
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 ps-3"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none"
                            placeholder="Search tours..." value="{{ request('search') }}"
                            @input.debounce.500ms="updateTours" @keydown.enter.prevent="">
                    </div>
                </div>

                <div class="vr d-none d-md-block p-0 my-2" style="height: 20px; opacity: 0.2"></div>

                <!-- Location Filter -->
                <div class="col-md-2">
                    <select name="place" class="form-select border-0 bg-transparent shadow-none text-muted"
                        @change="updateTours">
                        <option value="">All Locations</option>
                        @foreach($places as $place)
                            <option value="{{ $place->place_id }}" {{ request('place') == $place->place_id ? 'selected' : '' }}>
                                {{ $place->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sorting -->
                <div class="col-md-2">
                    <select name="sort" class="form-select border-0 bg-transparent shadow-none text-muted"
                        @change="updateTours">
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date: Soonest</option>
                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date: Latest</option>
                        <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                        <option value="alpha_asc" {{ request('sort') == 'alpha_asc' ? 'selected' : '' }}>Alphabetical</option>
                    </select>
                </div>

                <!-- Price Toggle -->
                <div class="col-md-auto ms-auto d-flex align-items-center bg-light rounded-pill px-3 py-1 me-2">
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" role="switch" name="price" value="free"
                            id="priceCheck" {{ request('price') === 'free' ? 'checked' : '' }}
                            @change="updateTours">
                        <label class="form-check-label small fw-bold text-muted" for="priceCheck">Free Only</label>
                    </div>
                </div>

                <!-- Submit (Hidden but accessible for Enter key on search) -->
                <button type="submit" class="d-none">Filter</button>
            </form>
        </div>

        <!-- Proposed Tours -->
        <div id="tours-list-wrapper" :class="{ 'opacity-50 pe-none': loading }" style="transition: opacity 0.3s ease;">
        <div class="section-container mb-5">
            <div class="d-flex align-items-center mb-4 border-bottom pb-2">
                <h3 id="upcoming-tours" class="fw-bold text-primary mb-0 me-3">Upcoming Tours</h3>
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
                            <div class="card h-100 shadow-sm border-0 card-hover bg-white position-relative">
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-3">{{ $tour->visitType->title }}</h5>
                                    <ul class="list-unstyled text-muted small mb-4">
                                        <li class="mb-2"><i class="bi bi-geo-alt me-1 text-primary"></i>
                                            {{ $tour->visitType->place->name }}</li>
                                        <li class="mb-2 d-flex align-items-center">
                                            <i class="bi bi-calendar3 me-2 text-primary"></i>
                                            <span>{{ \Carbon\Carbon::parse($tour->visit_date)->format('D, M j, Y') }}</span>
                                            @if($tour->is_imminent)
                                                <span class="badge bg-primary-subtle text-primary rounded-pill ms-2">Imminent</span>
                                            @endif
                                        </li>
                                        <li class="mb-2"><i class="bi bi-clock me-2 text-primary"></i>
                                            {{ \Carbon\Carbon::parse($tour->visitType->start_time)->format('g:i A') }}
                                            ({{ $tour->visitType->duration_minutes }} min)</li>
                                        <li class="mb-2 d-flex align-items-center">
                                            <i class="bi bi-people me-2 text-primary"></i>
                                            <span>{{ $tour->registrations->sum('num_participants') }} /
                                                {{ $tour->visitType->max_participants }} Filled</span>
                                            @if($tour->is_filling_fast)
                                                <span
                                                    class="badge bg-warning-subtle text-warning  rounded-pill ms-2">{{ $tour->spots_remaining }}
                                                    Spots Remaining</span>
                                            @endif
                                        </li>
                                        <li><i class="bi bi-map me-2 text-primary"></i> {{ $tour->visitType->meeting_point }}</li>
                                    </ul>

                                    <p class="card-text small text-muted line-clamp-3">
                                        {{ Str::limit($tour->visitType->description, 100) }}
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                                    @auth
                                        @if (Auth::user()->hasRole('Customer'))
                                            @php
                                                $isBooked = $tour->registrations->contains('user_id', Auth::id());
                                            @endphp

                                            @if($isBooked)
                                                <a href="{{ route('user.dashboard') . '?highlight=' . $tour->visit_id }}"
                                                    class="btn btn-outline-primary w-100 rounded-pill stretched-link">
                                                    Already Booked
                                                </a>
                                            @elseif($tour->registrations->sum('num_participants') >= $tour->visitType->max_participants)
                                                <button class="btn btn-secondary w-100 rounded-pill" disabled>
                                                    Sold Out
                                                </button>
                                            @else
                                                <a href="{{ route('visits.register.form', ['visit' => $tour->visit_id]) }}"
                                                    class="btn btn-primary w-100 rounded-pill stretched-link">
                                                    View Details & Book
                                                </a>
                                            @endif
                                        @else
                                            <button class="btn btn-secondary w-100 rounded-pill" disabled>Customer Only</button>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="btn btn-outline-primary w-100 rounded-pill stretched-link">Login to Book</a>
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
                </div>
            @endif
        </div>
        </div>

        <!-- Confirmed Tours        DA VALUTARE SE TENERLO O MENO, IN CASO SISTEMARLO -->
        @if(false && $confirmed_visits->isNotEmpty())
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
                                        <span class="badge bg-success rounded-pill px-3 py-2">Confirmed</span>
                                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>
                                            {{ $tour->visitType->place->name }}</small>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (window.location.search.includes("page=")) {
            document.getElementById('upcoming-tours').scrollIntoView({
                behavior: 'instant'
            })
        }
    });
</script>