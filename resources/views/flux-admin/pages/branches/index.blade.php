<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Branches</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $branches->count() }} branches in total</p>
        </div>
        <div>
            <a href="{{ route('flux-admin.branches.create') }}">
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New branch</flux:button>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($branches as $branch)
            <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('flux-admin.branches.show', $branch) }}" wire:navigate class="text-base font-bold text-zinc-900 dark:text-white hover:underline truncate block">{{ $branch->name }}</a>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $branch->address }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $branch->city }}{{ $branch->postal_code ? ', ' . $branch->postal_code : '' }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                        <flux:badge color="zinc" size="sm">
                            {{ $branch->motorbikes_count }} {{ Str::plural('motorbike', $branch->motorbikes_count) }}
                        </flux:badge>
                        <div class="flex gap-1">
                            <a href="{{ route('flux-admin.branches.edit', $branch) }}" wire:navigate>
                                <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                            </a>
                            <flux:button size="xs" variant="ghost" wire:click="delete({{ $branch->id }})" wire:confirm="Delete this branch?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
