@php($legacy = $legacy ?? [])

<div class="mt-10 border-t border-zinc-200 pt-10 dark:border-zinc-800">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
            {{ ($legacy['today'] ?? now())->format('d/m/Y') }}
            @if(optional(auth()->user())->first_name)
                — {{ auth()->user()->first_name }},
            @endif
            Administration overview
        </h2>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Same operational figures as the Backpack dashboard.</p>
    </div>

    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Club member visits</h3>
    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-flux-admin::stat-card label="{{ $legacy['club_visits']['day_before_label'] ?? '' }} visits" :value="number_format($legacy['club_visits']['day_before'] ?? 0)" icon="calendar" colour="indigo" :href="route('flux-admin.club-purchases.index')" />
        <x-flux-admin::stat-card label="Yesterday's visits" :value="number_format($legacy['club_visits']['yesterday'] ?? 0)" icon="calendar-days" colour="blue" :href="route('flux-admin.club-purchases.index')" />
        <x-flux-admin::stat-card label="Today's visits" :value="number_format($legacy['club_visits']['today'] ?? 0)" icon="calendar-days" colour="green" :href="route('flux-admin.club-purchases.index')" />
    </div>
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-flux-admin::stat-card label="All-time visits" :value="number_format($legacy['club_visits']['all_time'] ?? 0)" icon="chart-bar" colour="purple" :href="route('flux-admin.club-purchases.index')" />
        <x-flux-admin::stat-card label="This month's visits ({{ $legacy['club_visits']['this_month_label'] ?? now()->format('M Y') }})" :value="number_format($legacy['club_visits']['this_month'] ?? 0)" icon="calendar-days" colour="amber" :href="route('flux-admin.club-purchases.index')" />
    </div>

    <h3 class="mb-1 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Vehicle Sold</h3>
    <p class="mb-3 text-xs text-zinc-500 dark:text-zinc-400">Includes brand new cash, brand new payment plan, used cash, and used payment plan.</p>
    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-flux-admin::stat-card label="Total vehicles sold" :value="number_format($legacy['sales']['total'] ?? 0)" icon="chart-bar" colour="purple" :href="route('flux-admin.finance.index')" />
        <x-flux-admin::stat-card label="Vehicles sold this year ({{ $legacy['sales']['this_year_label'] ?? now()->format('Y') }})" :value="number_format($legacy['sales']['this_year'] ?? 0)" icon="calendar-days" colour="indigo" :href="route('flux-admin.finance.index')" />
        <x-flux-admin::stat-card label="Vehicles sold this month" :value="number_format($legacy['sales']['this_month'] ?? 0)" icon="calendar-days" colour="amber" :href="route('flux-admin.finance.index')" />
        <x-flux-admin::stat-card label="Vehicles sold last month" :value="number_format($legacy['sales']['last_month'] ?? 0)" icon="chart-bar" colour="blue" :href="route('flux-admin.finance.index')" />
    </div>
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-flux-admin::stat-card label="Vehicles sold this week" :value="number_format($legacy['sales']['this_week'] ?? 0)" icon="chart-bar" colour="green" :href="route('flux-admin.finance.index')" />
        <x-flux-admin::stat-card label="Vehicles sold last week" :value="number_format($legacy['sales']['last_week'] ?? 0)" icon="chart-bar" colour="indigo" :href="route('flux-admin.finance.index')" />
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-flux-admin::stat-card label="Regular customers" :value="number_format($legacy['total_customers'] ?? 0)" icon="users" colour="blue" :href="route('flux-admin.customers.index')" />
        <x-flux-admin::stat-card label="E-commerce subscribers" :value="number_format($legacy['total_ecommerce_customers'] ?? 0)" icon="shopping-cart" colour="amber" :href="route('flux-admin.customers.index')" />
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-flux-admin::stat-card label="Terminated payment plan" :value="number_format($legacy['finance']['terminated'] ?? 0)" icon="x-circle" colour="red" :href="route('flux-admin.finance.index')" />
        <x-flux-admin::stat-card label="Closed payment plan" :value="number_format($legacy['finance']['closed'] ?? 0)" icon="check-circle" colour="indigo" :href="route('flux-admin.finance.index')" />
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-flux-admin::stat-card label="Total service jobs" :value="number_format($legacy['repairs']['total'] ?? 0)" icon="wrench-screwdriver" colour="blue" :href="route('flux-admin.motorbike-repairs.index')" />
        <x-flux-admin::stat-card label="Repairs completed" :value="number_format($legacy['repairs']['completed'] ?? 0)" icon="wrench" colour="green" :href="route('flux-admin.motorbike-repairs.index')" />
        <x-flux-admin::stat-card label="Bikes delivered" :value="number_format($legacy['repairs']['delivered'] ?? 0)" icon="truck" colour="indigo" :href="route('flux-admin.motorbike-repairs.index')" />
    </div>

    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">PCN cases</h3>
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-flux-admin::stat-card label="Total PCN cases" :value="number_format($legacy['pcn']['total'] ?? 0)" icon="ticket" colour="blue" :href="route('flux-admin.pcn.index')" />
        <x-flux-admin::stat-card label="Closed cases" :value="number_format($legacy['pcn']['closed'] ?? 0)" icon="check-circle" colour="green" :href="route('flux-admin.pcn.index')" />
        <x-flux-admin::stat-card label="Police PCN cases" :value="number_format($legacy['pcn']['police'] ?? 0)" icon="shield-check" colour="indigo" :href="route('flux-admin.pcn.index')" />
    </div>

    @if(!empty($legacy['pcn_chart']['labels']))
        <div class="mb-6">
            <h3 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-white">PCN cases trend (last 6 months)</h3>
            <x-flux-admin::flux-chart-component
                canvas-id="flux-pcn-trend-chart"
                type="line"
                :labels="$legacy['pcn_chart']['labels']"
                :datasets="$legacy['pcn_chart']['datasets']"
                height="280px"
            />
        </div>
    @endif

    @if(($legacy['bikes_for_sale'] ?? collect())->isNotEmpty())
        <div class="mb-6 flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Bikes available for sale</h3>
            </div>
            <div class="touch-pan-x overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Registration</flux:table.column>
                        <flux:table.column>Make</flux:table.column>
                        <flux:table.column>Model</flux:table.column>
                        <flux:table.column>Year</flux:table.column>
                        <flux:table.column>Price</flux:table.column>
                        <flux:table.column>Branch</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($legacy['bikes_for_sale'] as $bike)
                            <flux:table.row>
                                <flux:table.cell class="font-mono text-xs">{{ strtoupper($bike->reg_no ?? '') }}</flux:table.cell>
                                <flux:table.cell>{{ $bike->make }}</flux:table.cell>
                                <flux:table.cell>{{ $bike->model }}</flux:table.cell>
                                <flux:table.cell>{{ $bike->year }}</flux:table.cell>
                                <flux:table.cell>£{{ number_format((float) ($bike->price ?? 0), 2) }}</flux:table.cell>
                                <flux:table.cell>{{ $bike->branch_name }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    @endif

    @if(!empty($legacy['fleet_chart']['labels']) || !empty($legacy['fleet_chart_values']))
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            @if(!empty($legacy['fleet_chart']['labels']))
                <div>
                    <h3 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-white">In-house motorbikes</h3>
                    <x-flux-admin::flux-chart-component
                        canvas-id="flux-fleet-pie-chart"
                        type="pie"
                        :labels="$legacy['fleet_chart']['labels']"
                        :datasets="$legacy['fleet_chart']['datasets']"
                        height="240px"
                    />
                </div>
            @endif
            @if(!empty($legacy['fleet_chart_values']))
                <div>
                    <h3 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-white">Fleet availability</h3>
                    <x-flux-admin::flux-chart-component
                        canvas-id="flux-fleet-bar-chart"
                        type="bar"
                        :labels="['For rent', 'Rented', 'For sale', 'Sold', 'Repairs', 'Cat B', 'Claim', 'Impounded', 'Accident', 'Missing', 'Stolen']"
                        :datasets="[[ 'label' => 'NGN motorcycle fleet', 'data' => $legacy['fleet_chart_values'], 'borderWidth' => 1 ]]"
                        height="240px"
                    />
                </div>
            @endif
        </div>
    @endif

    <div class="mb-6 border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
        <h3 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-white">{{ number_format($legacy['payment_count'] ?? 0) }} rental payments outstanding</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 text-left dark:border-zinc-700">
                        <th class="py-2 pr-4">Outstanding</th>
                        <th class="py-2 pr-4">Rentals</th>
                        <th class="py-2">Deposits</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-2 pr-4 font-semibold text-red-600 dark:text-red-400">£{{ $legacy['outstanding_total'] ?? '0.00' }}</td>
                        <td class="py-2 pr-4 text-red-600 dark:text-red-400">
                            <a href="{{ route('flux-admin.rental-due-payments.index') }}" class="underline">£{{ number_format((float) ($legacy['outstanding_rentals'] ?? 0), 2) }}</a>
                        </td>
                        <td class="py-2 text-red-600 dark:text-red-400">
                            <a href="{{ route('flux-admin.rental-due-payments.index') }}" class="underline">£{{ number_format((float) ($legacy['outstanding_deposits'] ?? 0), 2) }}</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @php($fleet = $legacy['fleet_counts'] ?? [])
    <div class="mb-6 flux-admin-table-panel border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="touch-pan-x overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>For rent</flux:table.column>
                    <flux:table.column>Rented</flux:table.column>
                    <flux:table.column>For sale</flux:table.column>
                    <flux:table.column>Sold</flux:table.column>
                    <flux:table.column>Repairs</flux:table.column>
                    <flux:table.column>Cat B</flux:table.column>
                    <flux:table.column>Claim</flux:table.column>
                    <flux:table.column>Impounded</flux:table.column>
                    <flux:table.column>Accident</flux:table.column>
                    <flux:table.column>Missing</flux:table.column>
                    <flux:table.column>Stolen</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell><a href="{{ route('flux-admin.motorbikes.index', ['q' => 'rent']) }}" class="text-brand-red underline">{{ $fleet['for_rent'] ?? 0 }}</a></flux:table.cell>
                        <flux:table.cell>{{ $fleet['rented'] ?? 0 }}</flux:table.cell>
                        <flux:table.cell><a href="{{ route('flux-admin.motorbike-for-sale.index') }}" class="text-brand-red underline">{{ $fleet['for_sale'] ?? 0 }}</a></flux:table.cell>
                        <flux:table.cell>{{ $fleet['sold'] ?? 0 }}</flux:table.cell>
                        <flux:table.cell><a href="{{ route('flux-admin.motorbike-repairs.index') }}" class="text-brand-red underline">{{ $fleet['repairs'] ?? 0 }}</a></flux:table.cell>
                        <flux:table.cell><a href="{{ route('flux-admin.motorbike-cat-b.index') }}" class="text-brand-red underline">{{ $fleet['cat_b'] ?? 0 }}</a></flux:table.cell>
                        <flux:table.cell><a href="{{ route('flux-admin.motorbike-claims.index') }}" class="text-brand-red underline">{{ $fleet['claim'] ?? 0 }}</a></flux:table.cell>
                        <flux:table.cell>{{ $fleet['impounded'] ?? 0 }}</flux:table.cell>
                        <flux:table.cell>{{ $fleet['accident'] ?? 0 }}</flux:table.cell>
                        <flux:table.cell>{{ $fleet['missing'] ?? 0 }}</flux:table.cell>
                        <flux:table.cell>{{ $fleet['stolen'] ?? 0 }}</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-white">TAX due soon</h3>
            @if(($legacy['tax_due'] ?? collect())->isNotEmpty())
                <ul class="space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                    @foreach($legacy['tax_due'] as $m)
                        <li>{{ $m->registration ?? $m->id }} — {{ $m->tax_due_date ?? 'N/A' }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-zinc-500 dark:text-zinc-400">None in the next 10 days.</p>
            @endif
        </div>
        <div class="border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-white">MOT due soon</h3>
            @if(($legacy['mot_due'] ?? collect())->isNotEmpty())
                <ul class="space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                    @foreach($legacy['mot_due'] as $m)
                        <li>{{ $m->registration ?? $m->id }} — {{ $m->mot_expiry_date ?? 'N/A' }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-zinc-500 dark:text-zinc-400">None in the next 10 days.</p>
            @endif
        </div>
    </div>
</div>
