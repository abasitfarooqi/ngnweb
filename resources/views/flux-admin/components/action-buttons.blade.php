@props([
    'edit' => null,
    'view' => null,
    'delete' => null,
])

<div class="flux-admin-actions flex flex-nowrap items-center justify-end gap-1.5">
    @if($view)
        <flux:button size="xs" variant="ghost" :href="$view" icon="eye" class="!rounded-none" title="View record" aria-label="View record">
            <span class="hidden sm:inline">View</span>
        </flux:button>
    @endif
    @if($edit)
        <flux:button size="xs" variant="ghost" :href="$edit" icon="pencil-square" class="!rounded-none" title="Edit record" aria-label="Edit record">
            <span class="hidden sm:inline">Edit</span>
        </flux:button>
    @endif
    {{ $slot }}
    @if($delete)
        <flux:button size="xs" variant="danger" :wire:click="$delete['action']" icon="trash" class="!rounded-none" title="Delete record" aria-label="Delete record">
            <span class="hidden sm:inline">Delete</span>
        </flux:button>
    @endif
</div>
