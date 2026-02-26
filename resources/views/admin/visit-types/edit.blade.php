@extends('layouts.app')

@section('title', __('messages.admin.visit_types.edit_title', ['title' => $visit_type->title]))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                 <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h4 class="fw-bold text-primary mb-0">{{ __('messages.admin.visit_types.edit_title', ['title' => $visit_type->title]) }}</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.visit-types.update', $visit_type) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('place_id') is-invalid @enderror" id="place_id" name="place_id" required>
                                        <option value="" disabled>{{ __('messages.admin.visit_types.select_place') }}</option>
                                        @foreach($places as $id => $name)
                                            <option value="{{ $id }}" {{ old('place_id', $visit_type->place_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="place_id">{{ __('messages.admin.visit_types.location_label') }}</label>
                                    @error('place_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" placeholder="{{ __('messages.admin.visit_types.title_placeholder') }}" value="{{ old('title', $visit_type->title) }}" required>
                                    <label for="title">{{ __('messages.admin.visit_types.title_label') }}</label>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="{{ __('messages.admin.visit_types.description_placeholder') }}" style="height: 100px;">{{ old('description', $visit_type->description) }}</textarea>
                            <label for="description">{{ __('messages.admin.visit_types.description_label') }}</label>
                             @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                             <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('meeting_point') is-invalid @enderror" id="meeting_point" name="meeting_point" placeholder="{{ __('messages.admin.visit_types.meeting_point_placeholder') }}" value="{{ old('meeting_point', $visit_type->meeting_point) }}" required>
                                    <label for="meeting_point">{{ __('messages.admin.visit_types.meeting_point_label') }}</label>
                                    @error('meeting_point')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                             </div>
                             <div class="col-md-3">
                                 <div class="form-floating">
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" placeholder="{{ __('messages.admin.visit_types.time_placeholder') }}" value="{{ old('start_time', \Carbon\Carbon::parse($visit_type->start_time)->format('H:i')) }}" required>
                                    <label for="start_time">{{ __('messages.admin.visit_types.time_label') }}</label>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                             </div>
                             <div class="col-md-3">
                                 <div class="form-floating">
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" id="duration_minutes" name="duration_minutes" placeholder="{{ __('messages.admin.visit_types.duration_placeholder') }}" value="{{ old('duration_minutes', $visit_type->duration_minutes) }}" min="1" required>
                                    <label for="duration_minutes">{{ __('messages.admin.visit_types.duration_label') }}</label>
                                     @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                             </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('period_start') is-invalid @enderror" id="period_start" name="period_start" placeholder="{{ __('messages.admin.visit_types.start_placeholder') }}" value="{{ old('period_start', $visit_type->period_start->format('Y-m-d')) }}" required>
                                    <label for="period_start">{{ __('messages.admin.visit_types.start_label') }}</label>
                                    @error('period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('period_end') is-invalid @enderror" id="period_end" name="period_end" placeholder="{{ __('messages.admin.visit_types.end_placeholder') }}" value="{{ old('period_end', $visit_type->period_end->format('Y-m-d')) }}" required>
                                    <label for="period_end">{{ __('messages.admin.visit_types.end_label') }}</label>
                                    @error('period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4 align-items-center">
                             <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('min_participants') is-invalid @enderror" id="min_participants" name="min_participants" placeholder="{{ __('messages.admin.visit_types.min_placeholder') }}" value="{{ old('min_participants', $visit_type->min_participants) }}" min="1" required>
                                    <label for="min_participants">{{ __('messages.admin.visit_types.min_label') }}</label>
                                </div>
                             </div>
                             <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control @error('max_participants') is-invalid @enderror" id="max_participants" name="max_participants" placeholder="{{ __('messages.admin.visit_types.max_placeholder') }}" value="{{ old('max_participants', $visit_type->max_participants) }}" min="1" required>
                                    <label for="max_participants">{{ __('messages.admin.visit_types.max_label') }}</label>
                                </div>
                             </div>
                        </div>

                        @if ($errors->has('general'))
                            <div class="alert alert-danger rounded-3" role="alert">
                                {{ $errors->first('general') }}
                            </div>
                        @endif

                        <div class="d-flex mt-4">
                            <a href="{{ route('admin.configurator') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">{{ __('messages.admin.visit_types.cancel_btn') }}</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">{{ __('messages.admin.visit_types.update_btn') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
