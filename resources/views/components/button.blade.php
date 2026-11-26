@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
])

@php
    $variantClass = $variant === 'secondary' ? 'btn-secondary' : 'btn-primary';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $variantClass]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $variantClass]) }}>
        {{ $slot }}
    </button>
@endif
