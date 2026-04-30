@props([
    'label' => null,
    'for' => null,
    'hint' => null,
    'required' => false,
    'error' => null,
])

<div {{ $attributes->class('flex flex-col gap-1') }}>
    @if($label)
        <label @if($for) for="{{ $for }}" @endif class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ $label }}
            @if($required)<span class="text-red-600">*</span>@endif
        </label>
    @endif

    {{ $slot }}

    @if($hint && ! $error)
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif
    @if($error)
        <p class="text-xs text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>
