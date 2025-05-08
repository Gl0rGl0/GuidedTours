<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @if($type === 'textarea')
        <textarea
            class="form-control @error($name) is-invalid @enderror"
            id="{{ $id }}"
            name="{{ $name }}"
            rows="{{ $rows ?? 3 }}" {{-- Allow overriding rows --}}
            {{ $attributes }} {{-- Include any additional attributes passed to the component --}}
        >{{ old($name, $value ?? '') }}</textarea> {{-- Use old() and allow default value --}}
    @else
        <input
            type="{{ $type }}"
            class="form-control @error($name) is-invalid @enderror"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ old($name, $value ?? '') }}" {{-- Use old() and allow default value --}}
            {{ $attributes }} {{-- Include any additional attributes passed to the component --}}
        >
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
