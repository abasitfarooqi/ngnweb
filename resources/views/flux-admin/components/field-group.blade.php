@props([
    'label' => null,
    'for' => null,
    'hint' => null,
    'required' => false,
    'error' => null,
    'span' => null,
])

@php
    $spanClass = match ($span) {
        '2', 2 => 'sm:col-span-2',
        '3', 3 => 'sm:col-span-2 md:col-span-3',
        'full' => 'col-span-full',
        default => '',
    };
@endphp

<div {{ $attributes->class(trim("flux-admin-field flex min-w-0 w-full flex-col gap-1 {$spanClass}")) }}>
    @if($label)
        <label @if($for) for="{{ $for }}" @endif class="text-sm font-medium leading-5 text-zinc-700 dark:text-zinc-300">
            {{ $label }}
            @if($required)<span class="text-red-600">*</span>@endif
        </label>
    @endif

    <div class="flux-admin-field-control min-w-0 w-full">
        {{ $slot }}
    </div>

    @if($hint && ! $error)
        <p class="text-xs leading-4 text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif
    @if($error)
        <p class="text-xs leading-4 text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>
