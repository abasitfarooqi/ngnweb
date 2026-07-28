<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">NGN Grid</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Welcome back, {{ auth()->user()->first_name }}. Here is your overview.</p>
        </div>
        <flux:button wire:click="refreshStats" icon="arrow-path" size="sm" variant="subtle">
            Refresh
        </flux:button>
    </div>

    {{-- Stat cards grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <x-flux-admin::stat-card
            label="Total NGN Vehicles"
            :value="number_format($stats['total_vehicles'])"
            icon="truck"
            colour="blue"
            :href="route('flux-admin.total-vehicles.index')"
        />
        <x-flux-admin::stat-card
            label="Active Rentals"
            :value="number_format($stats['active_rentals'])"
            icon="key"
            colour="green"
            :href="route('flux-admin.rentals.index')"
        />
        <x-flux-admin::stat-card
            label="Active Payment Plans"
            :value="number_format($stats['finance_applications'])"
            icon="banknotes"
            colour="purple"
            :href="route('flux-admin.finance.index', ['status' => 'active'])"
        />
        <x-flux-admin::stat-card
            label="Open PCN Cases"
            :value="number_format($stats['open_pcn_cases'])"
            icon="exclamation-triangle"
            colour="amber"
            :href="route('flux-admin.pcn.index', ['status' => 'open'])"
        />
        <x-flux-admin::stat-card
            label="Total Club Member"
            :value="number_format($stats['club_members'])"
            icon="star"
            colour="pink"
            :href="route('flux-admin.club.index')"
        />
    </div>

    @include('flux-admin.partials.dashboard-legacy', ['legacy' => $legacy])
</div>
