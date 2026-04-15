<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Branches</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $branches->count() }} branches in total</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($branches as $branch)
            <a href="{{ route('flux-admin.branches.show', $branch) }}" wire:navigate
               class="block border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-5 transition hover:border-zinc-400 dark:hover:border-zinc-500">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-zinc-900 dark:text-white truncate">{{ $branch->name }}</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $branch->address }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $branch->city }}{{ $branch->postal_code ? ', ' . $branch->postal_code : '' }}</p>
                    </div>
                    <flux:badge color="zinc" size="sm" class="flex-shrink-0">
                        {{ $branch->motorbikes_count }} {{ Str::plural('motorbike', $branch->motorbikes_count) }}
                    </flux:badge>
                </div>
            </a>
        @endforeach
    </div>
</div>
