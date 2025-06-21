@extends('layouts.app')

@section('title', 'Edit Place')

@section('content')
<div class="container">
    <h2>Edit Place: {{ $place->name }}</h2>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.places.update', $place) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Place Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $place->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location (Address or Coordinates)</label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $place->location) }}" required>
                     @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $place->description) }}</textarea>
                     @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                 @if ($errors->has('general'))
                    <div class="alert alert-danger" role="alert">
                        {{ $errors->first('general') }}
                    </div>
                @endif

                <a href="{{ route('admin.configurator') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Place</button>
            </form>
        </div>
    </div>
</div>
@endsection
