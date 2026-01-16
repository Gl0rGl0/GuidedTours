@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                         <h1 class="fw-bold text-primary mb-2">Terms of Service</h1>
                         <p class="text-muted fst-italic">Last Updated: June 2025</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead text-muted text-center">
                            This document defines the Terms of Service (ToS) for the web application developed by students 
                            <strong>Giorgio Felappi</strong>, <strong>Daniel Barbetti</strong>, and <strong>Leonardo Folgoni</strong>, 
                            as part of the <em>Web Programming</em> course.
                        </p>
                    </div>

                    <div class="vstack gap-4">
                        <section>
                            <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-bullseye me-2"></i>1. Purpose</h4>
                            <p class="text-secondary-emphasis">The software is designed exclusively for educational and demonstrative purposes, specifically for managing informational content, guided tours, and user roles. It is not intended for commercial use, nor is there any guarantee of availability or security for third parties.</p>
                        </section>

                        <section>
                            <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-copyright me-2"></i>2. Copyright & Intellectual Property</h4>
                            <p class="text-secondary-emphasis">The source code, user interface, and all original content are considered the work of the student authors. Usage, distribution, and modification are permitted exclusively for educational purposes with proper attribution.</p>
                        </section>

                        <section>
                             <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-shield-exclamation me-2"></i>3. Liability</h4>
                            <p class="text-secondary-emphasis">The software is provided "as is", without guarantees of continuous operation, data correctness, or system compatibility. The authors decline all responsibility for malfunctions, data loss, or damages resulting from application use.</p>
                        </section>

                         <section>
                            <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-person-lock me-2"></i>4. Privacy & Data</h4>
                            <p class="text-secondary-emphasis">The application may handle simulated data regarding users, visits, and preferences, but does not collect, store, or transmit real personal information. Any data entered is for demonstrative purposes only.</p>
                        </section>

                        <section>
                            <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-slash-circle me-2"></i>5. Usage Limitations</h4>
                            <p class="text-secondary-emphasis">Any use of the software outside the intended university scope, particularly for professional, advertising, or profiling activities, is prohibited.</p>
                        </section>
                        
                         <section>
                            <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-arrow-repeat me-2"></i>6. Updates</h4>
                            <p class="text-secondary-emphasis">The development team reserves the right to modify, integrate, or remove functionality without notice.</p>
                        </section>
                    </div>
                    
                    <hr class="my-5">
                    
                    <div class="text-center">
                        <p class="mb-3">Thank you for testing our application.</p>
                        <a href="https://github.com/Gl0rGl0/GuidedTours" target="_blank" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="bi bi-github me-2"></i> Visit Repository
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
