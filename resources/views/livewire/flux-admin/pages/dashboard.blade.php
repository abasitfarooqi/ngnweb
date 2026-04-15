<x-slot:breadcrumbs>
    <flux:breadcrumbs.item>Dashboard</flux:breadcrumbs.item>
</x-slot:breadcrumbs>

<x-slot:headerActions>
    <flux:button size="sm" variant="subtle" icon="arrow-path" wire:click="refreshStats">
        Refresh
    </flux:button>
</x-slot:headerActions>

<div>
    <flux:heading size="xl" class="mb-6">Dashboard</flux:heading>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-flux-admin.stat-card
            label="Total Motorbikes"
            :value="number_format($stats['total_motorbikes'])"
            icon="truck"
            color="blue"
        />
        <x-flux-admin.stat-card
            label="Active Rentals"
            :value="number_format($stats['active_rentals'])"
            icon="key"
            color="green"
        />
        <x-flux-admin.stat-card
            label="Weekly Revenue"
            :value="'£' . number_format($stats['weekly_revenue'], 2)"
            icon="currency-pound"
            color="emerald"
        />
        <x-flux-admin.stat-card
            label="Unpaid Invoices"
            :value="'£' . number_format($stats['unpaid_invoices'], 2)"
            icon="exclamation-circle"
            color="red"
        />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-flux-admin.stat-card
            label="Open PCN Cases"
            :value="number_format($stats['open_pcn_cases'])"
            icon="exclamation-triangle"
            color="amber"
        />
        <x-flux-admin.stat-card
            label="Active Finance"
            :value="number_format($stats['active_finance'])"
            icon="banknotes"
            color="purple"
        />
        <x-flux-admin.stat-card
            label="Total Customers"
            :value="number_format($stats['total_customers'])"
            icon="users"
            color="sky"
        />
        <x-flux-admin.stat-card
            label="Active Club Members"
            :value="number_format($stats['active_club_members'])"
            icon="star"
            color="yellow"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <flux:card>
            <flux:heading size="lg" class="mb-4">Quick Links</flux:heading>
            <div class="grid grid-cols-2 gap-3">
                <flux:button variant="subtle" href="{{ route('flux-admin.motorbikes.index') }}" icon="truck" class="justify-start">
                    Motorbikes
                </flux:button>
                <flux:button variant="subtle" href="{{ route('flux-admin.customers.index') }}" icon="users" class="justify-start">
                    Customers
                </flux:button>
                <flux:button variant="subtle" href="{{ route('flux-admin.rentals.index') }}" icon="key" class="justify-start">
                    Rentals
                </flux:button>
                <flux:button variant="subtle" href="{{ route('flux-admin.finance.index') }}" icon="banknotes" class="justify-start">
                    Finance
                </flux:button>
                <flux:button variant="subtle" href="{{ route('flux-admin.pcn.index') }}" icon="exclamation-triangle" class="justify-start">
                    PCN Cases
                </flux:button>
                <flux:button variant="subtle" href="{{ route('flux-admin.club.index') }}" icon="star" class="justify-start">
                    Club Members
                </flux:button>
                <flux:button variant="subtle" href="{{ route('flux-admin.branches.index') }}" icon="building-storefront" class="justify-start">
                    Branches
                </flux:button>
                <flux:button variant="subtle" href="/ngn-admin/dashboard" icon="arrow-left-start-on-rectangle" class="justify-start">
                    Backpack Admin
                </flux:button>
            </div>
        </flux:card>

        <flux:card>
            <flux:heading size="lg" class="mb-4">System Status</flux:heading>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Panel</span>
                    <flux:badge color="green" size="sm">Active</flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">User</span>
                    <span class="text-sm font-medium">{{ auth()->user()->full_name ?? auth()->user()->email }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Roles</span>
                    <span class="text-sm font-medium">{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'None' }}</span>
                </div>
            </div>
        </flux:card>
    </div>
</div>
