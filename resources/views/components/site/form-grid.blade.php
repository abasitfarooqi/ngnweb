@props(['cols' => 2, 'compact' => false])

@php
    $gridClass = match ((int) $cols) {
        3 => $compact
            ? 'site-form-grid site-form-grid-3-compact'
            : 'site-form-grid site-form-grid-3',
        default => 'site-form-grid site-form-grid-2',
    };
@endphp

<div {{ $attributes->merge(['class' => $gridClass]) }}>
    {{ $slot }}
</div>
