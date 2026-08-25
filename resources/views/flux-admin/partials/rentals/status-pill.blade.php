@php
    $tone = $tone ?? 'orange';
    $label = $label ?? '';
    $classes = match ($tone) {
        'green' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200',
        'red' => 'bg-red-100 text-red-800 dark:bg-red-950/40 dark:text-red-200',
        'orange' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-200',
        'blue' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/40 dark:text-sky-200',
        'indigo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-200',
        'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/40 dark:text-purple-200',
        default => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200',
    };
@endphp
<span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold {{ $classes }}">{{ $label }}</span>
