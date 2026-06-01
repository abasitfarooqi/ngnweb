<div>
    <x-flux-admin::data-table title="Contact queries" description="Messages submitted through the public contact form.">
        <x-slot:actions><x-flux-admin::export-button /></x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, email, phone or subject…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.is_dealt" placeholder="Dealt">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Dealt</flux:select.option>
                        <flux:select.option value="0">Pending</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Received</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Contact</flux:table.column>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column>Message</flux:table.column>
                <flux:table.column>Dealt</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="cq-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->created_at?->format('d M Y H:i') }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">{{ $r->name }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-400">{{ $r->phone }}<br>{{ $r->email }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-xs truncate">{{ $r->subject }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 max-w-md truncate">{{ $r->message }}</flux:table.cell>
                        <flux:table.cell><flux:switch :checked="(bool) $r->is_dealt" wire:click="toggleDealt({{ $r->id }})" /></flux:table.cell>
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.contact-queries.edit', $r->id) }}"><flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button></a>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">None.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[700px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">Edit contact query</flux:heading>

            <x-flux-admin::field-group label="Subject" :error="$errors->first('formData.subject')">
                <flux:input wire:model="formData.subject" />
            </x-flux-admin::field-group>

            <x-flux-admin::field-group label="Internal notes" :error="$errors->first('formData.notes')">
                <flux:textarea wire:model="formData.notes" rows="4" placeholder="Admin notes only…" />
            </x-flux-admin::field-group>

            <div class="flex items-center gap-2">
                <flux:checkbox wire:model="formData.is_dealt" id="cq_is_dealt" />
                <label for="cq_is_dealt" class="text-sm text-zinc-700 dark:text-zinc-300">Mark as dealt</label>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
