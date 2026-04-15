@props([
    'title',
    'subtitle' => null,
    'badges' => [],
    'backRoute' => null,
    'backLabel' => 'Back',
])

<div class="mb-6">
    @if($backRoute)
        <flux:button variant="subtle" size="sm" icon="arrow-left" href="{{ $backRoute }}" class="mb-3">
            {{ $backLabel }}
        </flux:button>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <flux:heading size="xl">{{ $title }}</flux:heading>
                @foreach($badges as $badge)
                    <flux:badge :color="$badge['color'] ?? 'zinc'" size="sm">
                        {{ $badge['label'] }}
                    </flux:badge>
                @endforeach
            </div>
            @if($subtitle)
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex items-center gap-2 flex-shrink-0">
                {{ $actions }}
            </div>
        @endisset
    </div>

    @isset($stats)
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
            {{ $stats }}
        </div>
    @endisset
</div>
