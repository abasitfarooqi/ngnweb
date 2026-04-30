@props([
    'title' => null,
    'description' => null,
    'actions' => null,
    'footer' => null,
])

<div {{ $attributes->class('flex flex-col gap-4') }}>
    @if($title || $description || $actions)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                @if($title)<h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $title }}</h1>@endif
                @if($description)<p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $description }}</p>@endif
            </div>
            @if($actions)<div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>@endif
        </div>
    @endif

    <div class="border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="flex items-center justify-end gap-2">{{ $footer }}</div>
    @endif
</div>
