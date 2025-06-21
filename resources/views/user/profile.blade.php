@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <span>{{ __('User Profile') }}</span>
        <button id="edit-btn" class="btn btn-sm btn-primary">Modifica</button>
      </div>
      <div class="card-body">
        <form id="profile-form" method="POST" action="{{ route('profile.update') }}">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label for="first_name" class="form-label">Nome</label>
            <input type="text"
                   id="first_name"
                   name="first_name"
                   value="{{ old('first_name', $user->first_name) }}"
                   class="form-control"
                   disabled>
          </div>

          <div class="mb-3">
            <label for="last_name" class="form-label">Cognome</label>
            <input type="text"
                   id="last_name"
                   name="last_name"
                   value="{{ old('last_name', $user->last_name) }}"
                   class="form-control"
                   disabled>
          </div>

          <div class="mb-3">
            <label for="birth_date" class="form-label">Data di nascita</label>
            <input type="date"
                   id="birth_date"
                   name="birth_date"
                   value="{{ old('birth_date', $user->birth_date) }}"
                   class="form-control"
                   disabled>
          </div>

          <div class="mt-3">
            <button type="submit" id="save-btn" class="btn btn-success" disabled>Salva</button>
            <a href="{{ route('change-password.form') }}" class="btn btn-secondary">Cambia Password</a>
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
    document.querySelectorAll('#profile-form input').forEach(i => i.disabled = false);
    document.getElementById('save-btn').disabled = false;
    this.disabled = true;
  });
</script>
@endpush
@endsection
