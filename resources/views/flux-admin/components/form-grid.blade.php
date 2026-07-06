@props(['cols' => 2, 'gap' => 4])

@php
    $cols = max(1, min(4, (int) $cols));
    $gap = (int) $gap;

    $gridClass = match ($cols) {
        1 => 'flux-admin-form-grid flux-admin-form-grid-1 grid grid-cols-1',
        3 => 'flux-admin-form-grid flux-admin-form-grid-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3',
        4 => 'flux-admin-form-grid flux-admin-form-grid-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
        default => 'flux-admin-form-grid flux-admin-form-grid-2 grid grid-cols-1 sm:grid-cols-2',
    };

    $gapClass = match ($gap) {
        3 => 'gap-3',
        6 => 'gap-6',
        default => 'gap-4',
    };
@endphp

<div {{ $attributes->merge(['class' => trim("$gridClass $gapClass")]) }}>
    {{ $slot }}
</div>
