@props([
    'label',
    'value',
    'icon' => null,
    'colour' => 'zinc',
    'trend' => null,
    'trendUp' => null,
])

@php
    $colourMap = [
        'blue'   => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
        'green'  => 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400',
        'purple' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400',
        'amber'  => 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400',
        'pink'   => 'bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400',
        'indigo' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400',
        'red'    => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
        'zinc'   => 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400',
    ];
    $iconClasses = $colourMap[$colour] ?? $colourMap['zinc'];
@endphp

<div {{ $attributes->merge(['class' => 'border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5']) }}>
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 truncate">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">{{ $value }}</p>

            @if($trend)
                <p class="mt-1 text-xs font-medium {{ $trendUp ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $trend }}
                </p>
            @endif
        </div>

        @if($icon)
            <div class="flex-shrink-0 p-2.5 {{ $iconClasses }}">
                <flux:icon :name="$icon" variant="outline" class="w-5 h-5" />
            </div>
        @endif
    </div>
</div>
