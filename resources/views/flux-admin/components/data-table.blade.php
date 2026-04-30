@props([
    'title' => null,
    'description' => null,
    'toolbar' => null,
    'actions' => null,
    'empty' => 'No records found.',
    'footer' => null,
])

<div {{ $attributes->class('flex flex-col gap-4') }}>
    @if($title || $description || $actions)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                @if($title)<h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $title }}</h1>@endif
                @if($description)<p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $description }}</p>@endif
            </div>
            @if($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endif
        </div>
    @endif

    @if($toolbar)
        <div class="flux-admin-toolbar border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
            {{ $toolbar }}
        </div>
    @endif

    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[44rem] md:min-w-0">
                {{ $slot }}
            </div>
        </div>
    </div>

    @if($footer)
        <div>{{ $footer }}</div>
    @endif
</div>
