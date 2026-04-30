<div>
    <x-flux-admin::data-table title="Support conversations" description="Customer chat threads routed through the support inbox.">
        <x-slot:actions>
            <flux:button size="sm" variant="ghost" :href="url(config('backpack.base.route_prefix').'/support-inbox')" icon="inbox" class="!rounded-none">Open inbox</flux:button>
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
                            <flux:button size="xs" variant="ghost" :href="url(config('backpack.base.route_prefix').'/support-inbox?conversation='.$r->id)" icon="chat-bubble-left-right" class="!rounded-none">Open</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No conversations.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
