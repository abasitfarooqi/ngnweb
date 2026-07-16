<div class="space-y-6">
    <div>
        <flux:heading size="xl">{{ $config['title'] }}</flux:heading>
        <flux:text class="mt-1">{{ $config['description'] }}</flux:text>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($config['links'] as $link)
            <a href="{{ route($link['route'], $link['params'] ?? []) }}" wire:navigate
                class="group block border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                <div class="flex items-start gap-3">
                    <flux:icon :name="$link['icon']" class="size-6 text-blue-600 dark:text-blue-400 shrink-0" />
                    <div class="font-semibold text-zinc-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $link['label'] }}</div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="border-t border-zinc-200 dark:border-zinc-800 pt-6">
        @include('flux-admin.pages.pcn.dashboard', ['embedded' => true])
    </div>
</div>
