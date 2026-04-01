@props([
    'variant' => 'default',
])

@php
    $btnClass = $variant === 'footer'
        ? 'p-2 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-500 transition'
        : 'p-2 text-gray-500 hover:text-brand-red dark:text-gray-400 dark:hover:text-white transition';
@endphp

<button
    type="button"
    x-data
    @click="window.ngnSetColourMode(document.documentElement.classList.contains('dark') ? 'light' : 'dark')"
    class="{{ $btnClass }}"
    aria-label="Toggle light and dark mode"
    title="Light or dark mode"
>
    {{-- Moon: show in light mode (switch to dark) --}}
    <svg class="h-5 w-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>
    {{-- Sun: show in dark mode (switch to light) --}}
    <svg class="h-5 w-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
</button>
