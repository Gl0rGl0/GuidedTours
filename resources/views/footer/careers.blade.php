@extends('layouts.app')

@section('title', 'Careers')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 text-center">
                <div class="card-body p-5">
                    <div class="mb-4 text-primary">
                        <i class="bi bi-people-fill display-1"></i>
                    </div>
                    
                    <h2 class="fw-bold text-primary mb-3">Join Our Team</h2>
                    <p class="lead text-muted mb-5">
                        We are a group of passionate students building the future of heritage discovery.
                    </p>

                    <div class="alert alert-light border shadow-sm text-start p-4 mb-4 rounded-3">
                        <h5 class="alert-heading fw-bold"><i class="bi bi-code-slash me-2 text-secondary"></i>Developers</h5>
                        <p class="mb-0 text-muted">While we are just a student project team, the collaboration and shared interest in web technologies have driven us to create this platform.</p>
                    </div>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="https://github.com/Gl0rGl0/GuidedTours" class="btn btn-outline-secondary rounded-pill px-4">
                           <i class="bi bi-github me-2"></i> Contribute on GitHub
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
