@props([
    'searchModel' => 'search',
    'searchPlaceholder' => 'Search…',
    'perPageModel' => 'perPage',
    'showReset' => true,
])

<div class="flex flex-col gap-3">
    <div class="min-w-0 w-full">
        <flux:input
            wire:model.live.debounce.300ms="{{ $searchModel }}"
            icon="magnifying-glass"
            placeholder="{{ $searchPlaceholder }}"
            variant="outline"
        />
    </div>

    <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        {{ $slot }}

        <div class="min-w-0 w-full">
            <select wire:model.live="{{ $perPageModel }}" class="w-full border border-zinc-300 bg-white px-2 py-2 text-sm text-zinc-900 hover:border-zinc-400 focus:border-zinc-600 focus:outline-none !rounded-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:hover:border-zinc-500 dark:focus:border-zinc-400">
                <option value="20">20 per page</option>
                <option value="50">50 per page</option>
                <option value="100">100 per page</option>
            </select>
        </div>

        @if($showReset)
            <div class="min-w-0 w-full">
                <flux:button wire:click="resetFilters" variant="ghost" size="sm" icon="x-mark" class="!rounded-none w-full hover:bg-zinc-100 dark:hover:bg-zinc-800" title="Reset filters">
                    <span>Reset</span>
                </flux:button>
            </div>
        @endif
    </div>
</div>
