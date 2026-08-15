<div class="space-y-6">
    <div>
        <flux:heading size="xl">Sale</flux:heading>
        <flux:text class="mt-1">Manage used motorcycle and brand new vehicle sales.</flux:text>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @php
            $links = [
                ['label' => 'Used Motorcycle Sale', 'description' => 'Add and edit used motorcycle sale records.', 'route' => 'flux-admin.motorbike-sales.index', 'icon' => 'arrow-trending-up'],
                ['label' => 'Brand New vehicles', 'description' => 'Manage brand new vehicles listed for sale.', 'route' => 'flux-admin.motorbike-for-sale.index', 'icon' => 'sparkles'],
            ];
        @endphp

        @foreach($links as $link)
            <a href="{{ route($link['route']) }}" wire:navigate class="group block border border-zinc-200 bg-white p-5 transition-colors hover:border-blue-500 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-blue-400">
                <div class="flex items-start gap-3">
                    <flux:icon :name="$link['icon']" class="size-6 shrink-0 text-blue-600 dark:text-blue-400" />
                    <div>
                        <div class="font-semibold text-zinc-900 group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">{{ $link['label'] }}</div>
                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $link['description'] }}</div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
