@props([
    'alt' => 'NGN Motors',
])

<div {{ $attributes->merge(['class' => 'w-full h-48 flex items-center justify-center bg-gray-50 dark:bg-gray-800/80 border-b border-gray-100 dark:border-gray-700']) }}>
    <img
        loading="lazy"
        src="{{ asset(config('site.logo', 'img/ngn-motor-logo-fit-small.png')) }}"
        alt="{{ $alt }}"
        class="h-16 sm:h-20 w-auto max-w-[75%] object-contain"
    >
</div>
