<div>
    <x-flux-admin::summary-header
        :title="$customer->full_name"
        :subtitle="$customer->email"
        backUrl="{{ route('flux-admin.customers.index') }}"
        backLabel="Back to Customers"
        :badges="array_filter([
            ['label' => ucfirst($customer->verification_status ?? 'unverified'), 'color' => match($customer->verification_status) {
                'verified' => 'green',
                'pending' => 'amber',
                'rejected' => 'red',
                default => 'zinc',
            }],
            $customer->is_club ? ['label' => 'Club Member', 'color' => 'green'] : null,
        ])"
    />

    {{-- Tabs --}}
    <div class="border-b border-zinc-200 dark:border-zinc-700 mb-6">
        <nav class="flex gap-0 -mb-px overflow-x-auto">
            @foreach(['profile' => 'Profile', 'addresses' => 'Addresses', 'documents' => 'Documents', 'bookings' => 'Bookings', 'finance' => 'Finance', 'pcn' => 'PCN Cases', 'club' => 'Club'] as $key => $label)
                <button
                    wire:click="setTab('{{ $key }}')"
                    class="px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition {{ $activeTab === $key ? 'border-zinc-900 dark:border-white text-zinc-900 dark:text-white' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-600' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab content --}}
    @switch($activeTab)
        @case('profile')
            <livewire:flux-admin.partials.customers.profile-section :customer="$customer" wire:key="tab-profile" />
            @break
        @case('addresses')
            <livewire:flux-admin.partials.customers.addresses-section :customer="$customer" wire:key="tab-addresses" />
            @break
        @case('documents')
            <livewire:flux-admin.partials.customers.documents-section :customer="$customer" wire:key="tab-documents" />
            @break
        @case('bookings')
            <livewire:flux-admin.partials.customers.bookings-section :customer="$customer" wire:key="tab-bookings" />
            @break
        @case('finance')
            <livewire:flux-admin.partials.customers.finance-section :customer="$customer" wire:key="tab-finance" />
            @break
        @case('pcn')
            <livewire:flux-admin.partials.customers.pcn-section :customer="$customer" wire:key="tab-pcn" />
            @break
        @case('club')
            <livewire:flux-admin.partials.customers.club-section :customer="$customer" wire:key="tab-club" />
            @break
    @endswitch
</div>
