@extends('layouts.app')

@section('title', __('messages.home.page_title'))

@section('full-width-content')

    <!-- Hero Section (Full Width with Background) -->
    <div class="hero-section bg-white border-bottom shadow-sm mb-5">
        <div class="container py-5">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-4 fw-bold mb-3 text-primary">{{ __('messages.home.hero.title_main') }} <br><span class="text-secondary">{{ __('messages.home.hero.title_highlight') }}</span></h1>
                    @auth
                        @if(Auth::user()->hasRole('Admin'))
                            <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                                {{ __('messages.home.hero.admin_welcome') }}
                            </p>
                        @elseif(Auth::user()->hasRole('Guide'))
                            <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                                {{ __('messages.home.hero.guide_welcome') }}
                            </p>
                        @elseif(Auth::user()->hasRole('Customer'))
                            <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                                {{ __('messages.home.hero.customer_welcome') }}
                            </p>
                        @else
                            <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                                {{ __('messages.home.hero.guest_welcome') }}
                            </p>
                        @endif
                    @else
                        <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                            {{ __('messages.home.hero.guest_welcome') }}
                        </p>
                    @endauth

                    @auth
                        @if(Auth::user()->hasRole('Customer'))
                            <div class="d-flex justify-content-center gap-3">
                                <a href="#tours-section" class="btn btn-primary shadow-sm px-4 rounded-pill">{{ __('messages.common.browse_tours') }}</a>
                                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary px-4 rounded-pill">{{ __('messages.common.my_dashboard') }}</a>
                            </div>
                        @else
                            <div class="d-flex justify-content-center gap-3">
                                <a href="#tours-section" class="btn btn-primary shadow-sm px-4 rounded-pill">{{ __('messages.common.browse_tours') }}</a>
                            </div>
                        @endif
                    @else
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('login') }}" class="btn btn-primary shadow-sm px-4 rounded-pill">{{ __('messages.common.login_to_book') }}</a>
                            <a href="{{ route('register') }}" class="btn btn-outline-secondary px-4 rounded-pill">{{ __('messages.common.register') }}</a>
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

        <!-- Filter Bar -->
        <div class="card shadow-sm border-0 mb-5 py-3 px-4 rounded-4 filter-bar">
            <form action="{{ route('home') }}" method="GET" x-ref="filterForm" @submit.prevent="updateTours" class="row g-2 align-items-center m-0">

                <!-- Search -->
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 ps-3"><i
                                class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none"
                            placeholder="{{ __('messages.home.filter.search_placeholder') }}" value="{{ request('search') }}"
                            @input.debounce.500ms="updateTours" @keydown.enter.prevent="">
                    </div>
                </div>

                <div class="vr d-none d-md-block p-0 my-2" style="height: 20px; opacity: 0.2"></div>

                <!-- Location Filter -->
                <div class="col-md-2">
                    <select name="place" class="form-select border-0 bg-transparent shadow-none text-muted"
                        @change="updateTours">
                        <option value="">{{ __('messages.home.filter.all_locations') }}</option>
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
                        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>{{ __('messages.home.filter.sort_date_asc') }}</option>
                        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>{{ __('messages.home.filter.sort_date_desc') }}</option>
                        <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>{{ __('messages.home.filter.sort_popularity') }}</option>
                        <option value="alpha_asc" {{ request('sort') == 'alpha_asc' ? 'selected' : '' }}>{{ __('messages.home.filter.sort_alpha_asc') }}</option>
                    </select>
                </div>

                <!-- Price Toggle -->
                <div class="col-md-auto ms-auto d-flex align-items-center bg-white rounded-pill px-3 py-1 me-2 shadow-sm filter-price-toggle">
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" role="switch" name="price" value="free"
                            id="priceCheck" {{ request('price') === 'free' ? 'checked' : '' }}
                            @change="updateTours">
                        <label class="form-check-label small fw-bold text-muted" for="priceCheck">{{ __('messages.home.filter.free_only') }}</label>
                    </div>
                </div>

                <!-- Submit (Hidden but accessible for Enter key on search) -->
                <button type="submit" class="d-none">{{ __('messages.common.filter') }}</button>
            </form>
        </div>

        <!-- Proposed Tours -->
        <div id="tours-list-wrapper" :class="{ 'opacity-50 pe-none': loading }" style="transition: opacity 0.3s ease;">
        <div class="section-container mb-5">
            <div class="d-flex align-items-center mb-4 border-bottom pb-2">
                <h3 id="upcoming-tours" class="fw-bold text-primary mb-0 me-3">{{ __('messages.home.tours.upcoming_title') }}</h3>
                <span class="badge bg-primary-subtle text-primary rounded-pill">{{ __('messages.common.open') }}</span>
            </div>

            @if($proposed_visits->isEmpty())
                <div class="col-12 text-center py-5 card border-0 shadow-sm" id="no-results-message">
                    <div class="card-body">
                        <x-empty-state 
                            icon="bi-calendar-x" 
                            title="{{ __('messages.home.tours.empty_state_title') }}" 
                            message="{{ __('messages.home.tours.empty_state_message') }}" 
                            :card="false" 
                        />
                    </div>
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="tours-container">
                    @foreach ($proposed_visits as $tour)
                        <div class="col tour-card">
                            <x-tour-card :visit="$tour" context="home" />
                        </div>
                    @endforeach
                </div>
            @endif
                </div>

                <!-- Load More Button -->
                <div class="mt-5 d-flex justify-content-center">
                    @if ($proposed_visits->hasMorePages())
                        <button type="button" class="btn load-more-btn rounded-pill px-4 py-2 fw-semibold load-more-btn-ajax"
                            data-next-page="{{ $proposed_visits->currentPage() + 1 }}" data-loading="false">
                            <span class="btn-content">
                                <i class="bi bi-arrow-down me-2" style="font-size: 0.9rem;"></i> {{ __('messages.common.show_more') }}
                            </span>
                            <span class="btn-loading" style="display: none;">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> {{ __('messages.common.loading') }}
                            </span>
                        </button>
                    @endif
                </div>
                </div>
                </div>
        </div>
        </div>

        <!-- Confirmed Tours        DA VALUTARE SE TENERLO O MENO, IN CASO SISTEMARLO -->
        @if(false && $confirmed_visits->isNotEmpty())
            <div class="section-container">
                <div class="d-flex align-items-center mb-4 border-bottom pb-2">
                    <h3 class="fw-bold text-success mb-0 me-3">{{ __('messages.home.tours.confirmed_title') }}</h3>
                    <span class="badge bg-success-subtle text-success rounded-pill">{{ __('messages.home.tours.participating_badge') }}</span>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach ($confirmed_visits as $tour)
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0 bg-white opacity-75">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="badge bg-success rounded-pill px-3 py-2">{{ __('messages.common.confirmed') }}</span>
                                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>
                                            {{ $tour->visitType->place->name }}</small>
                                    </div>
                                    <h5 class="card-title fw-bold mb-3">{{ $tour->visitType->title }}</h5>
                                    <p class="text-muted small mb-0">
                                        {{ __('messages.home.tours.confirmed_message') }} <br>
                                        {{ __('messages.home.tours.date_prefix') }} {{ \Carbon\Carbon::parse($tour->visit_date)->format('D, M j, Y') }}
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

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const loadMoreBtn = document.querySelector('.load-more-btn-ajax');
    const toursContainer = document.getElementById('tours-container');

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            if (loadMoreBtn.dataset.loading === 'true') return;

            const nextPage = parseInt(loadMoreBtn.dataset.nextPage);
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('page', nextPage);

            loadMoreBtn.dataset.loading = 'true';
            loadMoreBtn.querySelector('.btn-content').style.display = 'none';
            loadMoreBtn.querySelector('.btn-loading').style.display = 'inline';

            fetch(currentUrl.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTours = doc.querySelectorAll('.tour-card');

                    if (newTours.length > 0) {
                        newTours.forEach((tour, index) => {
                            toursContainer.appendChild(tour);
                        });

                        const nextPageBtn = doc.querySelector('.load-more-btn-ajax');
                        if (nextPageBtn) {
                            loadMoreBtn.dataset.nextPage = nextPageBtn.dataset.nextPage;
                            loadMoreBtn.dataset.loading = 'false';
                            loadMoreBtn.querySelector('.btn-content').style.display = 'inline';
                            loadMoreBtn.querySelector('.btn-loading').style.display = 'none';
                        } else {
                            loadMoreBtn.style.display = 'none';
                        }
                    } else {
                        loadMoreBtn.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error loading more tours:', error);
                    loadMoreBtn.dataset.loading = 'false';
                    loadMoreBtn.querySelector('.btn-content').style.display = 'inline';
                    loadMoreBtn.querySelector('.btn-loading').style.display = 'none';
                });
        });
    }
});
</script>
@endpush