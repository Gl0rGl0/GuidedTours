@extends('layouts.app')

@section('title', __('messages.footer.about.page_title'))

@section('content')
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold text-primary mb-4">{{ __('messages.footer.about.title') }}</h1>
            <p class="lead text-muted mb-4">
                {{ __('messages.footer.about.p1') }}
            </p>
            <p class="text-muted">
                {{ __('messages.footer.about.p2') }}
            </p>
        </div>
        <div class="col-lg-6">
            <div class="p-5 bg-light rounded-4 shadow-sm text-center">
                 <i class="bi bi-people-fill display-1 text-primary opacity-25"></i>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 text-center">
                <div class="mb-3 text-primary"><i class="bi bi-award fs-1"></i></div>
                <h5 class="fw-bold">{{ __('messages.footer.about.excellence_title') }}</h5>
                <p class="small text-muted mb-0">{{ __('messages.footer.about.excellence_desc') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 text-center">
                <div class="mb-3 text-secondary"><i class="bi bi-heart fs-1"></i></div>
                <h5 class="fw-bold">{{ __('messages.footer.about.passion_title') }}</h5>
                <p class="small text-muted mb-0">{{ __('messages.footer.about.passion_desc') }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 text-center">
                <div class="mb-3 text-primary"><i class="bi bi-globe-americas fs-1"></i></div>
                <h5 class="fw-bold">{{ __('messages.footer.about.accessibility_title') }}</h5>
                <p class="small text-muted mb-0">{{ __('messages.footer.about.accessibility_desc') }}</p>
            </div>
        </div>
    </div>
@endsection
