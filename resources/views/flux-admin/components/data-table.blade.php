@props([
    'title' => null,
    'description' => null,
    'toolbar' => null,
    'actions' => null,
    'empty' => 'No records found.',
    'footer' => null,
])

<div {{ $attributes->class('flex flex-col gap-4 flux-admin-responsive-table') }}>
    @if($title || $description || $actions)
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                @if($title)<h1 class="flux-admin-page-title text-2xl font-bold text-zinc-900 dark:text-white">{{ $title }}</h1>@endif
                @if($description)<p class="mt-1 max-w-3xl text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>@endif
            </div>
            @if($actions)
                <div class="flux-admin-actions flex w-full flex-wrap items-center gap-2 md:w-auto md:justify-end">{{ $actions }}</div>
            @endif
        </div>
    @endif

    @if($toolbar)
        <div class="flux-admin-toolbar border border-zinc-200 bg-white p-3 shadow-sm sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
            {{ $toolbar }}
        </div>
    @endif

    <div class="flux-admin-table-panel border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
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
