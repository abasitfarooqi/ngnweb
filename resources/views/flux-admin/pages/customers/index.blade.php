<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Customers</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Manage all registered customers.</p>
        </div>
    </div>

    <div class="flux-admin-toolbar mb-4 border border-zinc-200 bg-white p-3 sm:p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-stretch">
            <div class="min-w-0 w-full lg:flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by name, email or phone…" variant="filled" />
            </div>
            <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-stretch lg:w-auto lg:shrink-0">
                <div class="min-w-0 w-full sm:min-w-[11rem] sm:flex-1 lg:w-48 lg:flex-none">
                    <flux:select wire:model.live="filterVerification" placeholder="Verification">
                        <flux:select.option value="">All statuses</flux:select.option>
                        <flux:select.option value="verified">Verified</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="rejected">Rejected</flux:select.option>
                        <flux:select.option value="unverified">Unverified</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filterClub" placeholder="Club">
                        <flux:select.option value="">All members</flux:select.option>
                        <flux:select.option value="1">Club members</flux:select.option>
                        <flux:select.option value="0">Non-members</flux:select.option>
                    </flux:select>
                </div>
                <div class="min-w-0 w-full sm:basis-full sm:max-w-[10rem] lg:basis-auto lg:w-28">
                    <flux:select wire:model.live="perPage">
                        <flux:select.option value="20">20 per page</flux:select.option>
                        <flux:select.option value="50">50 per page</flux:select.option>
                        <flux:select.option value="100">100 per page</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </div>
    </div>

    <div class="flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[44rem] md:min-w-0">
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable :sorted="$sortField === 'first_name'" :direction="$sortField === 'first_name' ? $sortDirection : null" wire:click="sortBy('first_name')">Name</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'email'" :direction="$sortField === 'email' ? $sortDirection : null" wire:click="sortBy('email')">Email</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column sortable :sorted="$sortField === 'verification_status'" :direction="$sortField === 'verification_status' ? $sortDirection : null" wire:click="sortBy('verification_status')">Verification</flux:table.column>
                <flux:table.column>Club Member</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($customers as $customer)
                    <flux:table.row wire:key="customer-{{ $customer->id }}">
                        <flux:table.cell>
                            <a href="{{ route('flux-admin.customers.show', $customer) }}" class="font-medium text-zinc-900 dark:text-white hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                                {{ $customer->full_name }}
                            </a>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $customer->email }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $customer->phone }}</flux:table.cell>
                        <flux:table.cell>
                            @php
                                $vColour = match($customer->verification_status) {
                                    'verified' => 'green',
                                    'pending' => 'amber',
                                    'rejected' => 'red',
                                    default => 'zinc',
                                };
                            @endphp
                            <flux:badge :color="$vColour" size="sm">{{ ucfirst($customer->verification_status ?? 'unverified') }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($customer->is_club)
                                <flux:badge color="green" size="sm">Yes</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">No</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                            {{ $customer->preferredBranch?->name ?? '—' }}
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                            No customers found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>
