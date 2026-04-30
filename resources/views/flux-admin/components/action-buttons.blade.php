@props([
    'edit' => null,
    'view' => null,
    'delete' => null,
])

<div class="flex items-center gap-1">
    @if($view)
        <flux:button size="xs" variant="ghost" :href="$view" icon="eye" class="!rounded-none">View</flux:button>
    @endif
    @if($edit)
        <flux:button size="xs" variant="ghost" :href="$edit" icon="pencil-square" class="!rounded-none">Edit</flux:button>
    @endif
    {{ $slot }}
    @if($delete)
        <flux:button size="xs" variant="danger" :wire:click="$delete['action']" icon="trash" class="!rounded-none">Delete</flux:button>
    @endif
</div>
