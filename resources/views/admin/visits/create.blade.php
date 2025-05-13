@extends('layouts.app')

@section('title', 'Add New Visit')

@section('content')
  <div class="container">
    <h2>Add New Visit</h2>

    <div class="card">
      <div class="card-body">
        <form action="{{ route('admin.visits.store') }}" method="POST">
        @csrf

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="visit_type_id" class="form-label">VisitType</label>
            <select class="form-select @error('visit_type_id') is-invalid @enderror" id="visit_type_id" name="visit_type_id" required>
              <option value="">-- Select VisitType --</option>
              @foreach($visitTypes as $visitType)
                <option value="{{ $visitType->visit_type_id }}">{{ $visitType->title }}</option>
              @endforeach
            </select>

            @error('visit_type_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label for="visit_date" class="form-label">Date</label>
            <input type="date" class="form-control @error('visit_date') is-invalid @enderror" id="visit_date" name="visit_date" value="{{ old('visit_date') }}" required>
            @error('visit_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label for="assigned_volunteer_id" class="form-label">Available Volunteer</label>
            <select class="form-select" id="assigned_volunteer_id" name="assigned_volunteer_id" required>
              <option value="">-- Select Volunteer --</option>
            </select>
          </div>
        </div>

        @if ($errors->has('general'))
          <div class="alert alert-danger" role="alert">
          {{ $errors->first('general') }}
          </div>
        @endif

        <a href="{{ route('admin.visit-planning.index') }}" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Create Visit</button>
        </form>
      </div>
    </div>
  </div>

  <script>
  document.getElementById('visit_date').addEventListener('change', function () {
      const date = this.value;
      const volunteerSelect = document.getElementById('assigned_volunteer_id');

      // Svuota il dropdown
      volunteerSelect.innerHTML = '<option value="">-- Select Volunteer --</option>';

      if (!date) return;

      fetch(`/admin/volunteers/available?visit_date=${date}`)
          .then(response => response.json())
          .then(data => {
              data.forEach(volunteer => {
                  const option = document.createElement('option');
                  option.value = volunteer.user_id;
                  option.textContent = volunteer.username;
                  volunteerSelect.appendChild(option);
              });
          })
          .catch(error => {
              console.error('Error fetching volunteers:', error);
          });
  });
</script>

@endsection