@extends('layouts.app')

@section('title', 'Edit Visit Type')

@section('content')
<div class="container">
    <h2>Edit Visit Type: {{ $visit_type->title }}</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.visit-types.update', $visit_type) }}" method="POST">
                @csrf
                @method('PUT')

                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="place_id" class="form-label">Place</label>
                        <select class="form-select @error('place_id') is-invalid @enderror" id="place_id" name="place_id" required>
                            <option value="">-- Select Place --</option>
                            @foreach($places as $id => $name)
                                <option value="{{ $id }}" {{ old('place_id', $visit_type->place_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('place_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">Visit Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $visit_type->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $visit_type->description) }}</textarea>
                     @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="meeting_point" class="form-label">Meeting Point</label>
                        <input type="text" class="form-control @error('meeting_point') is-invalid @enderror" id="meeting_point" name="meeting_point" value="{{ old('meeting_point', $visit_type->meeting_point) }}" required>
                        @error('meeting_point')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     <div class="col-md-3 mb-3">
                        <label for="start_time" class="form-label">Start Time (HH:MM)</label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($visit_type->start_time)->format('H:i')) }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="duration_minutes" class="form-label">Duration (Minutes)</label>
                        <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $visit_type->duration_minutes) }}" min="1" required>
                        @error('duration_minutes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="period_start" class="form-label">Available From</label>
                        <input type="date" class="form-control @error('period_start') is-invalid @enderror" id="period_start" name="period_start" value="{{ old('period_start', $visit_type->period_start->format('Y-m-d')) }}" required>
                        @error('period_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="period_end" class="form-label">Available Until</label>
                        <input type="date" class="form-control @error('period_end') is-invalid @enderror" id="period_end" name="period_end" value="{{ old('period_end', $visit_type->period_end->format('Y-m-d')) }}" required>
                        @error('period_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                 <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="min_participants" class="form-label">Min Participants</label>
                        <input type="number" class="form-control @error('min_participants') is-invalid @enderror" id="min_participants" name="min_participants" value="{{ old('min_participants', $visit_type->min_participants) }}" min="1" required>
                        @error('min_participants')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="max_participants" class="form-label">Max Participants</label>
                        <input type="number" class="form-control @error('max_participants') is-invalid @enderror" id="max_participants" name="max_participants" value="{{ old('max_participants', $visit_type->max_participants) }}" min="1" required>
                        @error('max_participants')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     <div class="col-md-4 mb-3 align-self-center">
                         <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="requires_ticket" name="requires_ticket" value="1" {{ old('requires_ticket', $visit_type->requires_ticket) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_ticket">Requires Venue Ticket?</label>
                            <input type="hidden" name="requires_ticket" value="0">
                        </div>
                         @error('requires_ticket')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                 @if ($errors->has('general'))
                    <div class="alert alert-danger mt-3" role="alert">
                        {{ $errors->first('general') }}
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('admin.configurator') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Visit Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
