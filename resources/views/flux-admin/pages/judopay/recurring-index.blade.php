<div class="space-y-6">
    <div>
        <flux:heading size="xl">Recurring billing</flux:heading>
        <flux:text class="mt-1">Customers onboarded to Judopay recurring. Active rentals &amp; finance applications are loaded per row. Subscribe / CIT actions run in the legacy workflow.</flux:text>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <x-flux-admin::stat-card label="Total customers" :value="$stats['total_customers']" icon="users" />
        <x-flux-admin::stat-card label="Onboarded" :value="$stats['onboarded']" icon="check-badge" />
        <x-flux-admin::stat-card label="Not onboarded" :value="$stats['not_onboarded']" icon="exclamation-circle" />
    </div>

    <x-flux-admin::data-table>
        <x-slot:toolbar>
            <x-flux-admin::filter-bar search-placeholder="Search customer name or email…">
                <div class="min-w-0 w-full sm:min-w-[10rem] sm:flex-1 lg:w-40 lg:flex-none">
                    <flux:select wire:model.live="filters.onboarded" placeholder="Onboarding">
                        <flux:select.option value="">Any</flux:select.option>
                        <flux:select.option value="1">Onboarded</flux:select.option>
                        <flux:select.option value="0">Not onboarded</flux:select.option>
                    </flux:select>
                </div>
            </x-flux-admin::filter-bar>
        </x-slot:toolbar>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Onboarded</flux:table.column>
                <flux:table.column>Active rentals</flux:table.column>
                <flux:table.column>Finance</flux:table.column>
                <flux:table.column>Subscriptions</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($onboardings as $o)
                    @php
                        $customer = $o->onboardable;
                        $rentalItems = $customer?->renting_bookings?->flatMap->rentingBookingItems ?? collect();
                        $financeItems = $customer?->financeApplications?->flatMap->application_items ?? collect();
                    @endphp
                    <flux:table.row wire:key="rcr-{{ $o->id }}">
                        <flux:table.cell>
                            <div class="text-zinc-900 dark:text-white font-medium">{{ $customer?->first_name }} {{ $customer?->last_name }}</div>
                            <div class="text-xs text-zinc-500">{{ $customer?->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium {{ $o->is_onboarded ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200' : 'bg-zinc-100 dark:bg-zinc-900/30 text-zinc-800 dark:text-zinc-200' }}">
                                {{ $o->is_onboarded ? 'Yes' : 'No' }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell class="text-xs">
                            @foreach($rentalItems as $item)
                                <div class="font-mono">{{ $item->motorbike?->reg_no ?? '—' }} · £{{ number_format((float) $item->weekly_rent, 2) }}/wk</div>
                            @endforeach
                            @if($rentalItems->isEmpty())<span class="text-zinc-400">—</span>@endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs">
                            @foreach($financeItems as $item)
                                <div class="font-mono">{{ $item->motorbike?->reg_no ?? '—' }}</div>
                            @endforeach
                            @if($financeItems->isEmpty())<span class="text-zinc-400">—</span>@endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs">
                            {{ $o->subscriptions?->count() ?: 0 }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" icon="arrow-up-right" class="!rounded-none" href="{{ url('/admin/judopay/subscribe/'.$o->id) }}" target="_blank">Subscribe</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">No onboardings.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <x-slot:footer>{{ $onboardings->links() }}</x-slot:footer>
    </x-flux-admin::data-table>
</div>
