@extends('layouts.app')

@section('title', __('messages.admin.places.add_title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h4 class="fw-bold text-primary mb-0">{{ __('messages.admin.places.add_title') }}</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.places.store') }}" method="POST">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="{{ __('messages.admin.places.name_placeholder') }}" value="{{ old('name') }}" required>
                            <label for="name">{{ __('messages.admin.places.name_label') }}</label>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" placeholder="{{ __('messages.admin.places.location_placeholder') }}" value="{{ old('location') }}" required>
                            <label for="location">{{ __('messages.admin.places.location_label') }}</label>
                             @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-4">
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="{{ __('messages.admin.places.description_placeholder') }}" style="height: 120px;">{{ old('description') }}</textarea>
                            <label for="description">{{ __('messages.admin.places.description_label') }}</label>
                             @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                         @if ($errors->has('general'))
                            <div class="alert alert-danger rounded-3" role="alert">
                                {{ $errors->first('general') }}
                            </div>
                        @endif

                        <div class="d-flex mt-4">
                            <a href="{{ route('admin.configurator') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">{{ __('messages.admin.places.cancel_btn') }}</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">{{ __('messages.admin.places.save_btn') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
