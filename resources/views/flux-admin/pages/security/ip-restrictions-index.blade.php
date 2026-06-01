<div>
    <x-flux-admin::data-table
        title="IP restrictions"
        description="Allow or block specific IP addresses from reaching the admin panel or the full site."
    >
        <x-slot:actions>
            <a href="{{ route('flux-admin.ip-restrictions.create') }}">
                <flux:button size="sm" variant="primary" icon="plus" class="!rounded-none">New restriction</flux:button>
            </a>
        </x-slot:actions>

        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search IP or label…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.status" placeholder="Status">
                        <flux:select.option value="">All statuses</flux:select.option>
                        <flux:select.option value="allowed">Allowed</flux:select.option>
                        <flux:select.option value="blocked">Blocked</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-44 lg:flex-none">
                    <flux:select wire:model.live="filters.restriction_type" placeholder="Scope">
                        <flux:select.option value="">All scopes</flux:select.option>
                        <flux:select.option value="admin_only">Admin only</flux:select.option>
                        <flux:select.option value="full_site">Full site</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>

        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'ip_address'" :direction="$sortField === 'ip_address' ? $sortDirection : null" wire:click="sortBy('ip_address')">IP</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Scope</flux:table.column>
                <flux:table.column>Label</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Updated</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($restrictions as $r)
                    <flux:table.row wire:key="ipr-{{ $r->id }}">
                        <flux:table.cell class="font-mono text-xs text-zinc-900 dark:text-white">{{ $r->ip_address }}</flux:table.cell>
                        <flux:table.cell><x-flux-admin::status-badge :status="$r->status" /></flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ str_replace('_', ' ', $r->restriction_type) }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $r->label ?: '—' }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                            @if($r->user)
                                {{ trim(($r->user->first_name ?? '').' '.($r->user->last_name ?? '')) ?: $r->user->email }}
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400 whitespace-nowrap">{{ $r->updated_at?->format('d M Y H:i') }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('flux-admin.ip-restrictions.edit', $r->id) }}">
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                </a>
                                <flux:button size="xs" variant="ghost" wire:click="delete({{ $r->id }})" wire:confirm="Delete this restriction?" icon="trash" class="!rounded-none text-red-600 dark:text-red-400">Delete</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-zinc-500 dark:text-zinc-400">No IP restrictions configured.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <x-slot:footer>{{ $restrictions->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
