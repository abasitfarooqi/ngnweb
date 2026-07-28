@props(['total', 'label'])

<p {{ $attributes->merge(['class' => 'mt-1 text-sm text-zinc-500 dark:text-zinc-400']) }}>
    {{ number_format((int) $total) }} {{ $label }}
</p>
