@props(['title' => null, 'headingClass' => 'text-2xl font-bold text-gray-900 dark:text-white mb-6'])

<flux:card {{ $attributes->merge(['class' => 'p-6 md:p-8 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm']) }}>
    @if ($title)
        <h2 class="{{ $headingClass }}">{{ $title }}</h2>
    @endif
    {{ $slot }}
</flux:card>
