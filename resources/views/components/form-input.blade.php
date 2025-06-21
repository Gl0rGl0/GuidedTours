<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    {{-- Use old() and allow default value --}}
    @if($type === 'textarea')
        <textarea
            class="form-control @error($name) is-invalid @enderror"
            id="{{ $id }}"
            name="{{ $name }}"
            rows="{{ $rows ?? 3 }}"
            {{ $attributes }}
        >{{ old($name, $value ?? '') }}</textarea>
    @else
        <input
            type="{{ $type }}"
            class="form-control @error($name) is-invalid @enderror"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ old($name, $value ?? '') }}"
            {{ $attributes }}
        >
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
