<div class="space-y-6">
    <div>
        <flux:heading size="xl">MOT notifier stats</flux:heading>
        <flux:text class="mt-1">Vehicle MOT tracking with reminder dispatch status and WhatsApp outreach.</flux:text>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <x-flux-admin::stat-card label="Total vehicles" :value="$stats['total']" icon="clipboard-document-check" />
        <x-flux-admin::stat-card label="Expired MOT" :value="$stats['expired']" icon="exclamation-triangle" />
        <x-flux-admin::stat-card label="Due next 30 days" :value="$stats['upcoming_30']" icon="clock" />
        <x-flux-admin::stat-card label="WhatsApp sent" :value="$stats['whatsapp_sent']" icon="chat-bubble-left-ellipsis" />
    </div>

    <x-flux-admin::data-table>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search name, VRM or phone…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="MOT status">
                        <flux:select.option value="">All</flux:select.option>
                        <flux:select.option value="expired">Expired</flux:select.option>
                        <flux:select.option value="valid">Valid</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.whatsapp" placeholder="WhatsApp">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Sent</flux:select.option>
                        <flux:select.option value="0">Not sent</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>VRM</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>MOT due</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Last WhatsApp</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($rows as $n)
                    <flux:table.row wire:key="mot-{{ $n->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $n->motorbike_reg }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-900 dark:text-white">
                            {{ $n->customer_name }}
                            <div class="text-xs text-zinc-500">{{ $n->customer_contact ?: $n->customer_email }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">{{ $n->mot_due_date ? \Carbon\Carbon::parse($n->mot_due_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium {{ $n->mot_status === 'Expired' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200' }}">
                                {{ $n->mot_status }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-zinc-500">{{ $n->mot_last_whatsapp_notification_date ? \Carbon\Carbon::parse($n->mot_last_whatsapp_notification_date)->format('d M Y') : '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" icon="chat-bubble-left-ellipsis" class="!rounded-none" href="{{ $n->whatsapp_url }}" target="_blank">WhatsApp</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="markWhatsappSent({{ $n->id }})" icon="check" class="!rounded-none">Mark sent</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">No records.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $rows->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
