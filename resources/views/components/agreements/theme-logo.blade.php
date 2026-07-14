@props([
    'alt' => 'Neguinho Motors',
    'wrapClass' => 'agreement-brand-logo-wrap',
])
@php
    $light = asset(config('agreement.brand.web_logo_light', 'img/ngn-motor-logo-fit-small.png'));
    $dark = asset(config('agreement.brand.web_logo_dark', 'img/ngn-motor-logo-fit-small.png'));
@endphp
<span {{ $attributes->merge(['class' => $wrapClass]) }}>
    <img src="{{ $light }}" alt="{{ $alt }}" width="150" class="agreement-logo agreement-logo--light" style="max-width:150px;height:auto;">
    <img src="{{ $dark }}" alt="{{ $alt }}" width="150" class="agreement-logo agreement-logo--dark" style="max-width:150px;height:auto;">
</span>
