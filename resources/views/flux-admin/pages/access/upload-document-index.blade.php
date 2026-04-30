<div>
    <x-flux-admin::data-table title="Upload document links" description="Passcode URLs letting customers upload required documents.">
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">New link</flux:button>
        </x-slot:actions>
        <x-slot:toolbar><x-flux-admin::filter-bar search-placeholder="Search passcode, booking ID or customer…" /></x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Booking</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Passcode</flux:table.column>
                <flux:table.column>Expires</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="uda-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->booking_id ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->customer ? $r->customer->first_name.' '.$r->customer->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-zinc-700 dark:text-zinc-300">{{ $r->passcode }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->expires_at ? \Carbon\Carbon::parse($r->expires_at)->format('d M Y H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this link?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[560px]">
        <form wire:submit.prevent="saveForm" class="space-y-4">
            <flux:heading size="lg">{{ $recordId ? 'Edit link' : 'New upload link' }}</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-flux-admin::field-group label="Customer ID" :error="$errors->first('formData.customer_id')" required>
                    <flux:input type="number" wire:model="formData.customer_id" />
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Booking ID" :error="$errors->first('formData.booking_id')">
                    <flux:input type="number" wire:model="formData.booking_id" />
                </x-flux-admin::field-group>
            </div>
            <x-flux-admin::field-group label="Passcode" :error="$errors->first('formData.passcode')" required>
                <div class="flex gap-2">
                    <flux:input wire:model="formData.passcode" class="flex-1" />
                    <flux:button type="button" size="sm" variant="ghost" wire:click="regeneratePasscode" icon="arrow-path" class="!rounded-none">Regenerate</flux:button>
                </div>
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Expires at" :error="$errors->first('formData.expires_at')" required>
                <flux:input type="datetime-local" wire:model="formData.expires_at" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
