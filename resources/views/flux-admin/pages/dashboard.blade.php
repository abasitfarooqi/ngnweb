<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Dashboard</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Welcome back, {{ auth()->user()->first_name }}. Here is your overview.</p>
        </div>
        <flux:button wire:click="refreshStats" icon="arrow-path" size="sm" variant="subtle">
            Refresh
        </flux:button>
    </div>

    {{-- Stat cards grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <x-flux-admin::stat-card
            label="Total Motorbikes"
            :value="number_format($stats['total_motorbikes'])"
            icon="truck"
            colour="blue"
        />
        <x-flux-admin::stat-card
            label="Active Rentals"
            :value="number_format($stats['active_rentals'])"
            icon="key"
            colour="green"
        />
        <x-flux-admin::stat-card
            label="Finance Applications"
            :value="number_format($stats['finance_applications'])"
            icon="banknotes"
            colour="purple"
        />
        <x-flux-admin::stat-card
            label="Open PCN Cases"
            :value="number_format($stats['open_pcn_cases'])"
            icon="exclamation-triangle"
            colour="amber"
        />
        <x-flux-admin::stat-card
            label="Active Club Members"
            :value="number_format($stats['club_members'])"
            icon="star"
            colour="pink"
        />
        <x-flux-admin::stat-card
            label="Total Bookings"
            :value="number_format($stats['total_bookings'])"
            icon="calendar-days"
            colour="indigo"
        />
    </div>

    {{-- Quick navigation --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Quick Navigation</h2>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <a href="{{ route('flux-admin.motorbikes.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-700/50 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
                <flux:icon name="truck" variant="outline" class="w-5 h-5 text-zinc-400" />
                Motorbikes
            </a>
            <a href="{{ route('flux-admin.customers.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-700/50 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
                <flux:icon name="users" variant="outline" class="w-5 h-5 text-zinc-400" />
                Customers
            </a>
            <a href="{{ route('flux-admin.rentals.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-700/50 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
                <flux:icon name="key" variant="outline" class="w-5 h-5 text-zinc-400" />
                Rentals
            </a>
            <a href="{{ route('flux-admin.finance.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-700/50 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
                <flux:icon name="banknotes" variant="outline" class="w-5 h-5 text-zinc-400" />
                Finance
            </a>
            <a href="{{ route('flux-admin.pcn.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-700/50 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
                <flux:icon name="exclamation-triangle" variant="outline" class="w-5 h-5 text-zinc-400" />
                PCN Cases
            </a>
            <a href="{{ route('flux-admin.club.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-700/50 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition">
                <flux:icon name="star" variant="outline" class="w-5 h-5 text-zinc-400" />
                Club Members
            </a>
        </div>
    </div>
</div>
