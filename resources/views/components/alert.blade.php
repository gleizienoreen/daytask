@props([
    'type' => 'error',
])

@php
    $alertClass = $type === 'success' ? 'alert-success' : 'alert-error';
@endphp

<div role="alert" {{ $attributes->merge(['class' => $alertClass]) }}>
    {{ $slot }}
</div>
