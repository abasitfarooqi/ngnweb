@props([
    'alt' => 'Neguinho Motors',
    'wrapClass' => 'agreement-brand-logo-wrap',
])
@php
    $light = asset(config('agreement.brand.web_logo_light', 'img/ngn-motor-logo-fit-small.png'));
    $dark = asset(config('agreement.brand.web_logo_dark', 'img/ngn-motor-logo-fit-small.png'));
@endphp
<span {{ $attributes->merge(['class' => $wrapClass]) }}>
    <picture>
        <source srcset="{{ $dark }}" media="(prefers-color-scheme: dark)">
        <img src="{{ $light }}" alt="{{ $alt }}" width="150" style="max-width:150px;height:auto;">
    </picture>
</span>
