@props([
    'title' => null,
    'description' => null,
    'actions' => null,
    'footer' => null,
])

<div {{ $attributes->class('flex flex-col gap-4') }}>
    @if($title || $description || $actions)
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
                @if($title)<h1 class="flux-admin-page-title text-2xl font-bold text-zinc-900 dark:text-white">{{ $title }}</h1>@endif
                @if($description)<p class="mt-1 max-w-3xl text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>@endif
            </div>
            @if($actions)<div class="flux-admin-actions flex w-full flex-wrap items-center gap-2 md:w-auto md:justify-end">{{ $actions }}</div>@endif
        </div>
    @endif

    <div class="border border-zinc-200 bg-white p-4 shadow-sm sm:p-5 lg:p-6 dark:border-zinc-800 dark:bg-zinc-900">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="flux-admin-actions sticky bottom-0 z-10 -mx-3 flex flex-wrap items-center justify-end gap-2 border-t border-zinc-200 bg-zinc-100 px-3 py-3 dark:border-zinc-800 dark:bg-zinc-950 sm:static sm:mx-0 sm:border-0 sm:bg-transparent sm:p-0 sm:dark:bg-transparent">{{ $footer }}</div>
    @endif
</div>
