@props([
    'label' => null,
    'name',
    'id' => null,
    'type' => 'text',
    'value' => '',
])

@php
    $inputId = $id ?? $name;
@endphp

<div class="form-group">
    @if ($label)
        <label for="{{ $inputId }}" class="form-label">{{ $label }}</label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $inputId }}"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $attributes->merge(['class' => 'form-input']) }}
    >

    <p class="field-error hidden" data-error-for="{{ $name }}"></p>
</div>
