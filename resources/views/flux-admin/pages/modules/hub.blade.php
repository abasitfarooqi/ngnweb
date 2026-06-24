<div class="space-y-6">
    <div>
        <flux:heading size="xl">{{ $config['title'] }}</flux:heading>
        <flux:text class="mt-1">{{ $config['description'] }}</flux:text>
    </div>

    @if(! empty($config['stats']))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($config['stats'] as $stat)
                <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
                    <p class="text-xs text-zinc-500 uppercase tracking-wide">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($config['links'] as $link)
            <a href="{{ route($link['route']) }}" wire:navigate
                class="group block border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                <div class="flex items-start gap-3">
                    <flux:icon :name="$link['icon']" class="size-6 text-blue-600 dark:text-blue-400 shrink-0" />
                    <div class="font-semibold text-zinc-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $link['label'] }}</div>
                </div>
            </a>
        @endforeach
    </div>
</div>
