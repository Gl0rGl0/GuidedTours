@extends('layouts.app')

@section('title', __('messages.tours_views.register.page_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 text-center">
                    <h4 class="fw-bold text-primary mb-1">{{ __('messages.tours_views.register.title') }}</h4>
                    <p class="text-muted small">{{ __('messages.tours_views.register.description') }}</p>
                </div>
                <div class="card-body p-4">
                    @if ($visit)
                        <div class="bg-light rounded-3 p-4 mb-4">
                            <h5 class="fw-bold text-dark mb-3">{{ $visit->visitType->title }}</h5>
                            
                            <ul class="list-unstyled text-muted small mb-0">
                                <li class="mb-2 d-flex">
                                    <i class="bi bi-geo-alt me-2 text-primary flex-shrink-0"></i>
                                    <div>
                                        <strong>{{ $visit->visitType->place->name }}</strong>
                                        <div class="text-muted">{{ $visit->visitType->place->location }}</div>
                                    </div>
                                </li>
                                <li class="mb-2 d-flex pt-2 border-top">
                                     <i class="bi bi-calendar3 me-2 text-primary"></i>
                                     {{ \Carbon\Carbon::parse($visit->visit_date)->format('D, M j, Y') }}
                                </li>
                                <li class="mb-2 d-flex">
                                     <i class="bi bi-clock me-2 text-primary"></i>
                                     {{ \Carbon\Carbon::parse($visit->visitType->start_time)->format('g:i A') }} ({{ $visit->visitType->duration_minutes }} mins)
                                </li>
                                <li class="mb-2 d-flex">
                                    <i class="bi bi-people me-2 text-primary"></i>
                                    {{ $visit->registrations->sum('num_participants') }} / {{ $visit->visitType->max_participants }} {{ __('messages.tours_views.register.participants') }}
                                </li>
                                <li class="mb-2 d-flex">
                                    <i class="bi bi-tag me-2 text-primary"></i>
                                    <strong>{{ __('messages.tours_views.register.price') }}</strong> &nbsp; {{ $visit->visitType->price > 0 ? '€' . number_format($visit->visitType->price, 2) : __('messages.tours_views.register.free') }}
                                </li>
                            </ul>

                            <div class="alert alert-warning d-flex align-items-center mt-3 mb-0 py-2 small">
                                <i class="bi bi-ticket-perforated me-2"></i>
                                <div>{{ __('messages.tours_views.register.remember_ticket') }}</div>
                            </div>

                            <div class="alert alert-info d-flex align-items-center mt-3 mb-0 py-2 small fst-italic">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>{{ __('messages.tours_views.register.note_prenotation') }}</div>
                            </div>
                        </div>

                        <form action="{{ route('visits.register.submit', ['visit' => $visit->visit_id]) }}" method="POST">
                            @csrf
                            <div class="form-floating mb-4">
                                <input type="number" id="num_participants" name="num_participants" class="form-control" placeholder="{{ __('messages.tours_views.register.participants_placeholder') }}" min="1" value="1" required>
                                <label for="num_participants">{{ __('messages.tours_views.register.num_participants_label') }} ({{ $visit->getSpotsRemainingAttribute()}} {{ __('messages.tours_views.register.spots_remaining') }})</label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                    {{ __('messages.tours_views.register.submit_btn') }}
                                </button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill">{{ __('messages.tours_views.register.cancel_btn') }}</a>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-exclamation-circle text-muted display-1 mb-3"></i>
                            <p class="lead text-muted">{{ __('messages.tours_views.register.not_found') }}</p>
                            <a href="{{ route('home') }}" class="btn btn-primary rounded-pill">{{ __('messages.tours_views.register.back_to_home') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
