<div>
    <x-flux-admin::data-table title="Support conversations" description="Customer chat threads routed through the support inbox.">
        <x-slot:actions>
            <flux:button size="sm" variant="ghost" :href="route('flux-admin.support-inbox.index')" icon="inbox" class="!rounded-none">Open inbox</flux:button>
            <a href="{{ route('flux-admin.support-conversations.create') }}"><flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New conversation</flux:button></a>
        </x-slot:actions>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search title, topic or UUID…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">Any status</flux:select.option>
                        <flux:select.option value="open">Open</flux:select.option>
                        <flux:select.option value="closed">Closed</flux:select.option>
                        <flux:select.option value="archived">Archived</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'last_message_at'" :direction="$sortField === 'last_message_at' ? $sortDirection : null" wire:click="sortBy('last_message_at')">Last message</flux:table.column>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Topic</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Assigned</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="sc-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->last_message_at?->format('d M Y H:i') ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-md truncate">{{ $r->title ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->topic }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->customerAuth?->email ?? '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->assignedBackpackUser ? $r->assignedBackpackUser->first_name.' '.$r->assignedBackpackUser->last_name : '—' }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->status" :map="['open' => ['colour' => 'emerald', 'label' => 'Open'], 'closed' => ['colour' => 'zinc', 'label' => 'Closed'], 'archived' => ['colour' => 'zinc', 'label' => 'Archived']]" /></flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button size="xs" variant="ghost" :href="route('flux-admin.support-inbox.index').'?conversation='.$r->id" icon="chat-bubble-left-right" class="!rounded-none">Open</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this conversation and all its messages?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No conversations.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>

    <flux:modal wire:model.self="showForm" class="md:w-[680px]">
        <form wire:submit.prevent="saveForm" class="space-y-4" novalidate>
            <flux:heading size="lg">New conversation</flux:heading>
            <x-flux-admin::field-group label="Title" :error="$errors->first('formData.title')">
                <flux:input wire:model="formData.title" placeholder="Subject of the conversation" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Topic" :error="$errors->first('formData.topic')">
                <flux:input wire:model="formData.topic" placeholder="e.g. billing, repairs" />
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Status" :error="$errors->first('formData.status')" required>
                <flux:select wire:model="formData.status">
                    <flux:select.option value="open">Open</flux:select.option>
                    <flux:select.option value="closed">Closed</flux:select.option>
                    <flux:select.option value="archived">Archived</flux:select.option>
                </flux:select>
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Customer auth ID" :error="$errors->first('formData.customer_auth_id')">
                <flux:input type="number" wire:model="formData.customer_auth_id" placeholder="Optional" />
            </x-flux-admin::field-group>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button type="submit" variant="primary" class="!rounded-none">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
