<div>
    <x-flux-admin::data-table
        title="IP restrictions"
        description="Allow or block specific IP addresses from reaching the admin panel or the full site."
    >
        <x-slot:actions>
            <flux:button size="sm" variant="primary" icon="plus" wire:click="openCreate" class="!rounded-none">
                New restriction
            </flux:button>
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

        @if(session('flux-admin.flash'))
            <div class="border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700 dark:border-green-900 dark:bg-green-950 dark:text-green-300">
                {{ session('flux-admin.flash') }}
            </div>
        @endif

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
                                <flux:button size="xs" variant="ghost" wire:click="openEdit({{ $r->id }})" icon="pencil-square" class="!rounded-none">Edit</flux:button>
                                <flux:button size="xs" variant="danger" wire:click="deleteRestriction({{ $r->id }})" wire:confirm="Delete this restriction?" icon="trash" class="!rounded-none">Delete</flux:button>
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

    <flux:modal wire:model="editorOpen" class="md:w-[32rem]">
        <div class="flex flex-col gap-4">
            <flux:heading size="lg">{{ $editingId ? 'Edit IP restriction' : 'New IP restriction' }}</flux:heading>

            <x-flux-admin::field-group label="IP address" required :error="$errors->first('form.ip_address')">
                <flux:input wire:model="form.ip_address" placeholder="192.168.1.1" />
            </x-flux-admin::field-group>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-flux-admin::field-group label="Status" :error="$errors->first('form.status')">
                    <flux:select wire:model="form.status">
                        <flux:select.option value="blocked">Blocked</flux:select.option>
                        <flux:select.option value="allowed">Allowed</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
                <x-flux-admin::field-group label="Scope" :error="$errors->first('form.restriction_type')">
                    <flux:select wire:model="form.restriction_type">
                        <flux:select.option value="full_site">Full site</flux:select.option>
                        <flux:select.option value="admin_only">Admin only</flux:select.option>
                    </flux:select>
                </x-flux-admin::field-group>
            </div>

            <x-flux-admin::field-group label="Label" :error="$errors->first('form.label')" hint="Short description for your records.">
                <flux:input wire:model="form.label" placeholder="Office router" />
            </x-flux-admin::field-group>

            <x-flux-admin::field-group label="Linked user ID" :error="$errors->first('form.user_id')" hint="Optional — tie this rule to a specific user.">
                <flux:input type="number" wire:model="form.user_id" />
            </x-flux-admin::field-group>

            <div class="flex items-center justify-end gap-2 pt-2">
                <flux:button size="sm" variant="ghost" wire:click="$set('editorOpen', false)" class="!rounded-none">Cancel</flux:button>
                <flux:button size="sm" variant="primary" wire:click="save" class="!rounded-none">Save</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
