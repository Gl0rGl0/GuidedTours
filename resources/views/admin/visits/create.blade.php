@extends('layouts.app')

@section('title', __('messages.admin.visits.add_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h4 class="fw-bold text-primary mb-0">{{ __('messages.admin.visits.title') }}</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.visits.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-floating mb-3">
                            <select class="form-select @error('visit_type_id') is-invalid @enderror" id="visit_type_id" name="visit_type_id" required>
                                <option value="" selected disabled>{{ __('messages.admin.visits.select_type') }}</option>
                                @foreach($visitTypes as $visitType)
                                    <option value="{{ $visitType->visit_type_id }}">{{ $visitType->title }}</option>
                                @endforeach
                            </select>
                            <label for="visit_type_id">{{ __('messages.admin.visits.type_label') }}</label>
                            <div class="form-text text-muted small ms-1">{{ __('messages.admin.visits.type_help') }}</div>
                            @error('visit_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="date" class="form-control @error('visit_date') is-invalid @enderror" id="visit_date" name="visit_date" value="{{ old('visit_date') }}" required>
                            <label for="visit_date">{{ __('messages.admin.visits.date_label') }}</label>
                            <div class="form-text text-muted small ms-1">{{ __('messages.admin.visits.date_help') }}</div>
                            @error('visit_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-4">
                            <select class="form-select" id="assigned_volunteer_id" name="assigned_volunteer_id" required>
                                <option value="">{{ __('messages.admin.visits.select_volunteer_first') }}</option>
                            </select>
                            <label for="assigned_volunteer_id">{{ __('messages.admin.visits.volunteer_label') }}</label>
                            <div class="form-text text-muted small ms-1">{{ __('messages.admin.visits.volunteer_help') }}</div>
                            <div style="font-style: italic;" class="form-text text-muted small ms-1">{{ __('messages.admin.visits.volunteer_note') }}</div>
                        </div>

                         @if ($errors->has('general'))
                            <div class="alert alert-danger rounded-3" role="alert">
                                {{ $errors->first('general') }}
                            </div>
                        @endif

                        <div class="d-flex mt-4">
                            <a href="{{ route('admin.visit-planning.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">{{ __('messages.admin.visits.cancel_btn') }}</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">{{ __('messages.admin.visits.save_btn') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script>
document.getElementById('visit_date').addEventListener('change', function () {
    const date = this.value;
    const volunteerSelect = document.getElementById('assigned_volunteer_id');

    // Reset dropdown
    volunteerSelect.innerHTML = `<option value="">{{ __('messages.admin.visits.select_volunteer') }}</option>`;

    if (!date) return;
    
    // Add loading state
    volunteerSelect.disabled = true;

    fetch(`/admin/volunteers/available?visit_date=${date}`)
        .then(response => response.json())
        .then(data => {
            volunteerSelect.disabled = false;
            if (data.length === 0) {
                 const option = document.createElement('option');
                  option.textContent = `{{ __('messages.admin.visits.no_volunteers') }}`;
                  option.disabled = true;
                  volunteerSelect.appendChild(option);
            } else {
                data.forEach(volunteer => {
                    const option = document.createElement('option');
                    option.value = volunteer.user_id;
                    option.textContent = volunteer.first_name + ' ' + volunteer.last_name;
                    volunteerSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            volunteerSelect.disabled = false;
            console.error('Error fetching volunteers:', error);
        });
});
</script>
@endsection