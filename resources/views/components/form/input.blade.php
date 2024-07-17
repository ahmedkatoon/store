{{-- @props(['type', 'name', 'value'])

<input type="{{ $type ?? 'text' }}" name="{{ $name }}" value="{{ old('name', $value) }}"
    class="form-control @error('name') is-invalid @enderror">
@error('name')
    <div class="text-danger">
        {{ $message }}
    </div>
@enderror --}}

@props([
    'type' => 'text', 'name', 'value' => '', 'label' => false
])

@if($label)
<label for="">{{ $label }}</label>
@endif

<input
    type="{{ $type }}"
    name="{{ $name }}"
    value="{{ old($name, $value) }}"
    {{ $attributes->class([
        'form-control',
        'is-invalid' => $errors->has($name)
    ]) }}
>

{{-- <x-form.validation-feedback :name="$name" /> --}}