<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Customers</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Manage all registered customers.</p>
        </div>
    </div>

    {{-- Filters row --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by name, email or phone…" icon="magnifying-glass" />
        </div>
        <flux:select wire:model.live="filterVerification" class="w-full sm:w-48">
            <option value="">All Statuses</option>
            <option value="verified">Verified</option>
            <option value="pending">Pending</option>
            <option value="rejected">Rejected</option>
            <option value="unverified">Unverified</option>
        </flux:select>
        <flux:select wire:model.live="filterClub" class="w-full sm:w-40">
            <option value="">All Members</option>
            <option value="1">Club Members</option>
            <option value="0">Non-Members</option>
        </flux:select>
        <flux:select wire:model.live="perPage" class="w-full sm:w-28">
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </flux:select>
    </div>

    {{-- Table --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-x-auto">
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

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>
