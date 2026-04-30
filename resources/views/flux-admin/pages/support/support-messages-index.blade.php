<div>
    <x-flux-admin::data-table title="Support messages" description="Flat log of every message across support conversations.">
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search body or conversation ID…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:input type="number" wire:model.live.debounce.500ms="filters.conversation_id" placeholder="Conversation" />
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.sender_type" placeholder="Sender">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="customer">Customer</flux:select.option>
                        <flux:select.option value="staff">Staff</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>When</flux:table.column>
                <flux:table.column>Conversation</flux:table.column>
                <flux:table.column>Sender</flux:table.column>
                <flux:table.column>Body</flux:table.column>
                <flux:table.column>Read (customer)</flux:table.column>
                <flux:table.column>Read (staff)</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $r)
                    <flux:table.row wire:key="sm-{{ $r->id }}">
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap text-xs">{{ $r->created_at?->format('d M Y H:i') }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">#{{ $r->conversation_id }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium {{ $r->sender_type === 'customer' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200' : 'bg-zinc-100 dark:bg-zinc-900/30 text-zinc-800 dark:text-zinc-200' }}">
                                {{ ucfirst($r->sender_type) }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white max-w-xl truncate">{{ Str::limit(strip_tags((string) $r->body), 140) }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">{{ $r->read_at_customer ? $r->read_at_customer->format('d M H:i') : '—' }}</flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">{{ $r->read_at_staff ? $r->read_at_staff->format('d M H:i') : '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No messages.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
