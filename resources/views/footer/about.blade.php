@extends('layouts.app')

@section('title', 'About Us')

@section('content')
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold text-primary mb-4">Discover Our Story</h1>
            <p class="lead text-muted mb-4">
                We are a passionate team dedicated to revealing the hidden history and vibrant culture of our city. 
                What started as a university project has grown into the premier platform for guided cultural experiences.
            </p>
            <p class="text-muted">
                Our mission is to connect improved access to cultural heritage with the enthusiasm of young volunteer guides. 
                We believe that every street corner has a story to tell, and we're here to help you hear it.
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
                <h5 class="fw-bold">Excellence</h5>
                <p class="small text-muted mb-0">Committed to providing high-quality, verified historical information.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 text-center">
                <div class="mb-3 text-secondary"><i class="bi bi-heart fs-1"></i></div>
                <h5 class="fw-bold">Passion</h5>
                <p class="small text-muted mb-0">Driven by a love for art, culture, and our local community.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 text-center">
                <div class="mb-3 text-primary"><i class="bi bi-globe-americas fs-1"></i></div>
                <h5 class="fw-bold">Accessibility</h5>
                <p class="small text-muted mb-0">Making cultural heritage accessible to everyone, everywhere.</p>
            </div>
        </div>
    </div>
@endsection
