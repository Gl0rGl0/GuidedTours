@extends('layouts.app')

@section('title', 'Add New Visit')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h4 class="fw-bold text-primary mb-0">Schedule New Visit</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.visits.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-floating mb-3">
                            <select class="form-select @error('visit_type_id') is-invalid @enderror" id="visit_type_id" name="visit_type_id" required>
                                <option value="" selected disabled>Select Visit Type</option>
                                @foreach($visitTypes as $visitType)
                                    <option value="{{ $visitType->visit_type_id }}">{{ $visitType->title }}</option>
                                @endforeach
                            </select>
                            <label for="visit_type_id">Visit Type</label>
                            <div class="form-text text-muted small ms-1">Choose the specific tour type to organize.</div>
                            @error('visit_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="date" class="form-control @error('visit_date') is-invalid @enderror" id="visit_date" name="visit_date" value="{{ old('visit_date') }}" required>
                            <label for="visit_date">Date</label>
                            <div class="form-text text-muted small ms-1">Select a date to fetch available volunteers.</div>
                            @error('visit_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-4">
                            <select class="form-select" id="assigned_volunteer_id" name="assigned_volunteer_id" required>
                                <option value="">-- Select Date First --</option>
                            </select>
                            <label for="assigned_volunteer_id">Available Volunteer</label>
                            <div class="form-text text-muted small ms-1">Only volunteers available on the selected date will appear here.</div>
                        </div>

                         @if ($errors->has('general'))
                            <div class="alert alert-danger rounded-3" role="alert">
                                {{ $errors->first('general') }}
                            </div>
                        @endif

                        <div class="d-flex mt-4">
                            <a href="{{ route('admin.visit-planning.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">Create Visit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('visit_date').addEventListener('change', function () {
    const date = this.value;
    const volunteerSelect = document.getElementById('assigned_volunteer_id');

    // Reset dropdown
    volunteerSelect.innerHTML = '<option value="">-- Select Volunteer --</option>';

    if (!date) return;
    
    // Add loading state
    volunteerSelect.disabled = true;

    fetch(`/admin/volunteers/available?visit_date=${date}`)
        .then(response => response.json())
        .then(data => {
            volunteerSelect.disabled = false;
            if (data.length === 0) {
                 const option = document.createElement('option');
                  option.textContent = "No volunteers available on this date";
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