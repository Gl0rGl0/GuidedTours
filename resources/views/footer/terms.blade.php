@extends('layouts.app')

@section('title', __('messages.footer.terms.page_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                         <h1 class="fw-bold text-primary mb-2">{{ __('messages.footer.terms.title') }}</h1>
                         <p class="text-muted fst-italic">{{ __('messages.footer.terms.last_updated') }}</p>
                    </div>

                    <div class="mb-5">
                        <p class="lead text-muted text-center">
                            {!! __('messages.footer.terms.intro') !!}
                        </p>
                    </div>

                    <div class="vstack gap-4">
                        <section>
                            <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-bullseye me-2"></i>{{ __('messages.footer.terms.s1_title') }}</h4>
                            <p class="text-secondary-emphasis">{{ __('messages.footer.terms.s1_desc') }}</p>
                        </section>

                        <section>
                            <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-copyright me-2"></i>{{ __('messages.footer.terms.s2_title') }}</h4>
                            <p class="text-secondary-emphasis">{{ __('messages.footer.terms.s2_desc') }}</p>
                        </section>

                        <section>
                             <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-shield-exclamation me-2"></i>{{ __('messages.footer.terms.s3_title') }}</h4>
                            <p class="text-secondary-emphasis">{{ __('messages.footer.terms.s3_desc') }}</p>
                        </section>

                         <section>
                            <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-person-lock me-2"></i>{{ __('messages.footer.terms.s4_title') }}</h4>
                            <p class="text-secondary-emphasis">{{ __('messages.footer.terms.s4_desc') }}</p>
                        </section>

                        <section>
                            <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-slash-circle me-2"></i>{{ __('messages.footer.terms.s5_title') }}</h4>
                            <p class="text-secondary-emphasis">{{ __('messages.footer.terms.s5_desc') }}</p>
                        </section>
                        
                         <section>
                            <h4 class="fw-bold text-secondary mb-3"><i class="bi bi-arrow-repeat me-2"></i>{{ __('messages.footer.terms.s6_title') }}</h4>
                            <p class="text-secondary-emphasis">{{ __('messages.footer.terms.s6_desc') }}</p>
                        </section>
                    </div>
                    
                    <hr class="my-5">
                    
                    <div class="text-center">
                        <p class="mb-3">{{ __('messages.footer.terms.thanks') }}</p>
                        <a href="https://github.com/Gl0rGl0/GuidedTours" target="_blank" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="bi bi-github me-2"></i> {{ __('messages.footer.terms.github_btn') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
