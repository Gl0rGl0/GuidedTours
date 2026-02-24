@extends('layouts.app')

@section('title', 'Edit Visit Type')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                 <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h4 class="fw-bold text-primary mb-0">Edit Visit Type: {{ $visit_type->title }}</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.visit-types.update', $visit_type) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('place_id') is-invalid @enderror" id="place_id" name="place_id" required>
                                        <option value="" disabled>Select Place</option>
                                        @foreach($places as $id => $name)
                                            <option value="{{ $id }}" {{ old('place_id', $visit_type->place_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="place_id">Location</label>
                                    @error('place_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" placeholder="Title" value="{{ old('title', $visit_type->title) }}" required>
                                    <label for="title">Visit Title</label>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Desc" style="height: 100px;">{{ old('description', $visit_type->description) }}</textarea>
                            <label for="description">Description</label>
                             @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                             <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('meeting_point') is-invalid @enderror" id="meeting_point" name="meeting_point" placeholder="Meeting Point" value="{{ old('meeting_point', $visit_type->meeting_point) }}" required>
                                    <label for="meeting_point">Meeting Point</label>
                                    @error('meeting_point')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                             </div>
                             <div class="col-md-3">
                                 <div class="form-floating">
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" placeholder="Time" value="{{ old('start_time', \Carbon\Carbon::parse($visit_type->start_time)->format('H:i')) }}" required>
                                    <label for="start_time">Start Time</label>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                             </div>
                             <div class="col-md-3">
                                 <div class="form-floating">
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" id="duration_minutes" name="duration_minutes" placeholder="Mins" value="{{ old('duration_minutes', $visit_type->duration_minutes) }}" min="1" required>
                                    <label for="duration_minutes">Duration (min)</label>
                                     @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                             </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('period_start') is-invalid @enderror" id="period_start" name="period_start" placeholder="Start" value="{{ old('period_start', $visit_type->period_start->format('Y-m-d')) }}" required>
                                    <label for="period_start">Available From</label>
                                    @error('period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('period_end') is-invalid @enderror" id="period_end" name="period_end" placeholder="End" value="{{ old('period_end', $visit_type->period_end->format('Y-m-d')) }}" required>
                                    <label for="period_end">Available Until</label>
                                    @error('period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4 align-items-center">
                             <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('min_participants') is-invalid @enderror" id="min_participants" name="min_participants" placeholder="Min" value="{{ old('min_participants', $visit_type->min_participants) }}" min="1" required>
                                    <label for="min_participants">Min Participants</label>
                                </div>
                             </div>
                             <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('max_participants') is-invalid @enderror" id="max_participants" name="max_participants" placeholder="Max" value="{{ old('max_participants', $visit_type->max_participants) }}" min="1" required>
                                    <label for="max_participants">Max Participants</label>
                                </div>
                             </div>
                             <div class="col-md-4">
                                <div class="form-check form-switch p-3 border rounded bg-light">
                                    <input class="form-check-input" type="checkbox" role="switch" id="requires_ticket" name="requires_ticket" value="1" {{ old('requires_ticket', $visit_type->requires_ticket) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="requires_ticket">Requires Venue Ticket</label>
                                    <input type="hidden" name="requires_ticket" value="0">
                                </div>
                             </div>
                        </div>

                        @if ($errors->has('general'))
                            <div class="alert alert-danger rounded-3" role="alert">
                                {{ $errors->first('general') }}
                            </div>
                        @endif

                        <div class="d-flex mt-4">
                            <a href="{{ route('admin.configurator') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">Update Visit Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
