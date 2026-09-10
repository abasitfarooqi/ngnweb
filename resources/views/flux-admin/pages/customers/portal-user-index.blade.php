<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3">
            <div class="flex size-11 items-center justify-center rounded-2xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950"><flux:icon name="users" class="size-5" /></div>
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Portal & customers</h1>
                <p class="mt-1 text-sm text-zinc-500">One workspace for registered users, normal customers, profile data, and access controls.</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-2 self-start rounded-full bg-zinc-100 px-3 py-1.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon name="lock-closed" class="size-3.5" /> Restricted: IDs 93, 66, 65</span>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex min-w-max items-center gap-2">
            <div class="w-[28rem] shrink-0">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search all customer data: name, email, licence, address, notes…" />
            </div>
            <div class="w-40 shrink-0">
                <flux:select wire:model.live="viewFilter">
                <flux:select.option value="all">All types</flux:select.option>
                <flux:select.option value="portal">Portal users</flux:select.option>
                <flux:select.option value="registered">Frontend registered</flux:select.option>
                <flux:select.option value="normal">Normal customers</flux:select.option>
                <flux:select.option value="inactive">Inactive type</flux:select.option>
                </flux:select>
            </div>
            <div class="w-36 shrink-0">
                <flux:select wire:model.live="accountFilter"><flux:select.option value="all">Account: all</flux:select.option><flux:select.option value="active">Account active</flux:select.option><flux:select.option value="inactive">Account inactive</flux:select.option></flux:select>
            </div>
            <div class="w-36 shrink-0">
                <flux:select wire:model.live="portalFilter"><flux:select.option value="all">Portal: all</flux:select.option><flux:select.option value="active">Portal active</flux:select.option><flux:select.option value="inactive">Portal inactive</flux:select.option></flux:select>
            </div>
            <div class="w-36 shrink-0">
                <flux:select wire:model.live="uploadFilter"><flux:select.option value="all">Uploads: all</flux:select.option><flux:select.option value="allowed">Uploads allowed</flux:select.option><flux:select.option value="disabled">Uploads disabled</flux:select.option></flux:select>
            </div>
            <div class="w-36 shrink-0">
                <flux:select wire:model.live="verificationFilter"><flux:select.option value="all">Email: all</flux:select.option><flux:select.option value="verified">Email verified</flux:select.option><flux:select.option value="unverified">Email unverified</flux:select.option></flux:select>
            </div>
            <flux:button type="button" wire:click="resetFilters" variant="ghost" icon="x-mark" class="shrink-0 rounded-xl">Reset</flux:button>
        </div>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Registration</flux:table.column>
                <flux:table.column>Customer active</flux:table.column>
                <flux:table.column>Portal</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Uploads</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($customers as $customer)
                    <flux:table.row wire:key="portal-user-{{ $customer->id }}">
                        <flux:table.cell>
                            <div class="font-medium">{{ $customer->full_name }}</div>
                            <div class="text-xs text-zinc-500">ID #{{ $customer->id }}</div>
                            <div class="text-xs text-zinc-500">{{ $customer->phone ?: 'No phone' }}</div>
                            <div class="max-w-xs truncate text-xs text-zinc-500" title="{{ $customer->email }}">{{ $customer->email ?: 'No email' }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($customer->is_register)
                                <flux:badge color="green">Frontend registered</flux:badge>
                            @else
                                <flux:badge color="zinc">Normal customer</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $customer->is_active ? 'Active' : 'Inactive' }}</flux:table.cell>
                        <flux:table.cell>{{ $customer->customerAuth && $customer->customerAuth->is_active && $customer->is_register ? 'Active' : 'Inactive' }}</flux:table.cell>
                        <flux:table.cell>
                            <span class="inline-flex items-center gap-1 text-xs {{ $customer->customerAuth?->email_verified_at ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}"><flux:icon name="{{ $customer->customerAuth?->email_verified_at ? 'check-circle' : 'x-circle' }}" class="size-4" /> {{ $customer->customerAuth?->email_verified_at ? 'Verified' : 'Not verified' }}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{ $customer->portal_upload_access ? 'Verified / allowed' : 'Not allowed' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                <a href="{{ route('flux-admin.portal-users.edit', $customer) }}"><flux:button size="xs" variant="primary" icon="pencil-square" class="rounded-xl">Edit portal profile</flux:button></a>
                                <flux:button size="xs" variant="ghost" wire:click="toggleCustomer({{ $customer->id }})">{{ $customer->is_active ? 'Deactivate customer' : 'Activate customer' }}</flux:button>
                                @if($customer->customerAuth)
                                    <flux:button size="xs" variant="ghost" wire:click="togglePortal({{ $customer->id }})">{{ $customer->customerAuth->is_active ? 'Deactivate portal' : 'Activate portal' }}</flux:button>
                                @endif
                                <flux:button size="xs" variant="ghost" wire:click="toggleUploadAccess({{ $customer->id }})">{{ $customer->portal_upload_access ? 'Remove upload right' : 'Allow uploads' }}</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="sendCredentials({{ $customer->id }})">Reset + send credentials</flux:button>
                                @if($customer->customerAuth)
                                    <flux:button size="xs" variant="ghost" wire:click="$set('selectedCustomerId', {{ $customer->id }})">Set password</flux:button>
                                @endif
                            </div>
                            @if($selectedCustomerId === $customer->id)
                                <div class="mt-2 flex max-w-md gap-2">
                                    <flux:input type="password" wire:model="newPassword" placeholder="New password" />
                                    <flux:input type="password" wire:model="newPassword_confirmation" placeholder="Confirm" />
                                    <flux:button size="sm" variant="primary" wire:click="setPassword({{ $customer->id }})">Save</flux:button>
                                </div>
                                @error('newPassword') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="7">No customers found for this filter.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
    {{ $customers->links() }}
</div>
