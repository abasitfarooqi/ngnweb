@props(['tag' => 'form'])

<{{ $tag }} {{ $attributes->merge(['class' => 'site-form site-form-stack']) }}>
    {{ $slot }}
</{{ $tag }}>
