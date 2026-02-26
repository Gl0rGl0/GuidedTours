@extends('layouts.app')

@section('title', __('messages.tours_views.customized_visits.page_title'))

@section('content')
    @php
        $user = Auth::user();
        $pageTitle = __('messages.tours_views.customized_visits.title_default');
        if ($user->hasRole('Admin')) $pageTitle = __('messages.tours_views.customized_visits.title_admin');
        if ($user->hasRole('Guide')) $pageTitle = __('messages.tours_views.customized_visits.title_guide');
        if ($user->hasRole('Customer')) $pageTitle = __('messages.tours_views.customized_visits.title_customer');
    @endphp

    @if($user->hasRole('Guide'))
        <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex gap-3 align-items-start">
            <i class="bi bi-info-circle-fill fs-4 mt-1 flex-shrink-0"></i>
            <div>
                <strong>{{ __('messages.tours_views.customized_visits.guide_instructions_title') }}</strong>
                <ul class="mb-0 mt-1 small">
                    <li>{!! __('messages.tours_views.customized_visits.guide_instruction_1') !!}</li>
                    <li>{!! __('messages.tours_views.customized_visits.guide_instruction_2') !!}</li>
                    <li>{!! __('messages.tours_views.customized_visits.guide_instruction_3') !!}</li>
                    <li>{!! __('messages.tours_views.customized_visits.guide_instruction_4') !!}</li>
                </ul>
            </div>
        </div>
    @endif

    <div class="d-flex align-items-center mb-4 border-bottom pb-2">
        <h2 class="fw-bold text-primary mb-0 me-3">{{ $pageTitle }}</h2>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($visits as $visit)
            <div class="col">
                <x-tour-card :visit="$visit" context="archive" />
            </div>
        @empty
            <div class="col-12 text-center py-5 card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <x-empty-state 
                        icon="bi-clock-history" 
                        title="{{ __('messages.tours_views.customized_visits.empty_state_title') }}" 
                        message="{{ __('messages.tours_views.customized_visits.empty_state_message') }}" 
                        actionText="{{ __('messages.tours_views.customized_visits.back_to_home') }}"
                        actionUrl="{{ route('home') }}"
                        :card="false" 
                    />
                </div>
            </div>
        @endforelse
    </div>
@endsection
