@props([
    'icon' => 'information-circle',
    'label',
    'href' => null,
    'tone' => 'zinc',
])

@php
    $iconWrap = match ($tone) {
        'green' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
        'blue' => 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300',
        'indigo' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300',
        'purple' => 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300',
        'amber' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300',
        'red' => 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-300',
        default => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3']) }}>
    <div class="shrink-0 p-2 {{ $iconWrap }}">
        <flux:icon :name="$icon" variant="outline" class="w-4 h-4" />
    </div>
    <div class="min-w-0">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $label }}</p>
        <div class="mt-0.5 text-sm font-semibold text-zinc-900 dark:text-white break-words">
            @if($href)
                <a href="{{ $href }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $slot }}</a>
            @else
                {{ $slot }}
            @endif
        </div>
    </div>
</div>
