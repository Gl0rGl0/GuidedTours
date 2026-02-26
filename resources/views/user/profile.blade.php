@extends('layouts.app')

@section('title', __('messages.user.profile.page_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold text-primary mb-0">{{ __('messages.user.profile.title') }}</h4>
                    <button id="edit-btn" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-pencil me-1"></i> {{ __('messages.user.profile.edit_btn') }}
                    </button>
                </div>
                
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
                             {{ strtoupper(substr($user->first_name, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h5>
                        <p class="text-muted small badge bg-light text-dark shadow-sm border">{{ ucfirst(Auth::user()->getRoleNames()->first()) }} {{ __('messages.user.profile.account_type') }}</p>
                    </div>

                    <form id="profile-form" method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="{{ __('messages.user.profile.first_name') }}" value="{{ old('first_name', $user->first_name) }}" disabled>
                            <label for="first_name">{{ __('messages.user.profile.first_name') }}</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="{{ __('messages.user.profile.last_name') }}" value="{{ old('last_name', $user->last_name) }}" disabled>
                            <label for="last_name">{{ __('messages.user.profile.last_name') }}</label>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="date" class="form-control" id="birth_date" name="birth_date" placeholder="{{ __('messages.user.profile.birth_date') }}" value="{{ old('birth_date', $user->birth_date) }}" disabled>
                            <label for="birth_date">{{ __('messages.user.profile.birth_date') }}</label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                             <a href="{{ route('change-password.form') }}" class="btn btn-link text-decoration-none text-muted p-0">
                                <i class="bi bi-key me-1"></i> {{ __('messages.user.profile.change_password') }}
                            </a>
                            <button type="submit" id="save-btn" class="btn btn-primary rounded-pill px-4 shadow-sm" disabled>
                                {{ __('messages.user.profile.save_btn') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
<script>
    document.getElementById('edit-btn').addEventListener('click', function(e) {
        e.preventDefault();
        // Enable inputs
        document.querySelectorAll('#profile-form input').forEach(i => {
            i.disabled = false;
            i.classList.add('bg-white'); // Ensure they look editable
        });
        
        // Toggle buttons
        const saveBtn = document.getElementById('save-btn');
        saveBtn.disabled = false;
        
        // Hide edit button
        this.style.display = 'none';
        
        // Focus first field
        document.getElementById('first_name').focus();
    });
</script>
@endpush
@endsection
