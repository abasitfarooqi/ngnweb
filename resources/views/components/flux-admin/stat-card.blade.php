@props([
    'label',
    'value',
    'icon' => null,
    'color' => 'zinc',
])

@php
    $colorClasses = match($color) {
        'blue' => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20',
        'green' => 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20',
        'emerald' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20',
        'red' => 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20',
        'amber' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20',
        'purple' => 'text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20',
        'sky' => 'text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-900/20',
        'yellow' => 'text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20',
        default => 'text-zinc-600 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-900/20',
    };
@endphp

<flux:card class="flex items-center gap-4">
    @if($icon)
        <div class="flex-shrink-0 p-3 rounded-lg {{ $colorClasses }}">
            <flux:icon :name="$icon" class="size-6" />
        </div>
    @endif
    <div class="min-w-0">
        <p class="text-sm text-zinc-500 dark:text-zinc-400 truncate">{{ $label }}</p>
        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $value }}</p>
    </div>
</flux:card>
