@props(['tag' => 'form'])

<{{ $tag }} {{ $attributes->merge(['class' => 'site-form']) }}>
    {{ $slot }}
</{{ $tag }}>
