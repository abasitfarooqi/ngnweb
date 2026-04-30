@props([
    'searchModel' => 'search',
    'searchPlaceholder' => 'Search…',
    'perPageModel' => 'perPage',
    'showReset' => true,
])

<div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-stretch">
    <div class="min-w-0 w-full lg:flex-1">
        <flux:input wire:model.live.debounce.300ms="{{ $searchModel }}" placeholder="{{ $searchPlaceholder }}" variant="filled" />
    </div>

    <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch lg:w-auto lg:shrink-0">
        {{ $slot }}

        <div class="min-w-0 w-full sm:basis-full sm:max-w-[10rem] lg:basis-auto lg:w-28">
            <flux:select wire:model.live="{{ $perPageModel }}">
                <flux:select.option value="20">20 per page</flux:select.option>
                <flux:select.option value="50">50 per page</flux:select.option>
                <flux:select.option value="100">100 per page</flux:select.option>
            </flux:select>
        </div>

        @if($showReset)
            <div class="min-w-0 w-full sm:max-w-[8rem] lg:w-auto">
                <flux:button wire:click="resetFilters" variant="ghost" size="sm" class="!rounded-none w-full">
                    Reset
                </flux:button>
            </div>
        @endif
    </div>
</div>
