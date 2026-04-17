@props([
    'title',
    'subtitle' => null,
    'badges' => [],
    'backUrl' => null,
    'backLabel' => 'Back',
])

<div {{ $attributes->merge(['class' => 'border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5 mb-6']) }}>
    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
        <div class="flex-1 min-w-0">
            @if($backUrl)
                <a href="{{ $backUrl }}" class="inline-flex items-center gap-1 text-xs font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 mb-2 transition">
                    <flux:icon name="arrow-left" variant="micro" class="w-3 h-3" />
                    {{ $backLabel }}
                </a>
            @endif

            <h1 class="text-xl font-bold text-zinc-900 dark:text-white truncate">{{ $title }}</h1>

            @if($subtitle)
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $subtitle }}</p>
            @endif

            @if(count($badges))
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($badges as $badge)
                        <flux:badge :color="$badge['color'] ?? 'zinc'" size="sm">
                            {{ $badge['label'] }}
                        </flux:badge>
                    @endforeach
                </div>
            @endif
        </div>

        @if(isset($actions))
            <div class="flex items-center gap-2 flex-shrink-0">
                {{ $actions }}
            </div>
        @endif
    </div>

    @if(isset($stats))
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-zinc-200 dark:border-zinc-700">
            {{ $stats }}
        </div>
    @endif
</div>
