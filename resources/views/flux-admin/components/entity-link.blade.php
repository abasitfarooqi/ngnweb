@props(['href', 'label'])

<a href="{{ $href }}" wire:navigate {{ $attributes->merge(['class' => 'text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline']) }}>
    {{ $label }} ↗
</a>
