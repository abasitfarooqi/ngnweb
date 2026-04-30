<div class="space-y-6">
    <x-flux-admin::summary-header title="Active bookings summary" subtitle="Breakdown of every posted, open-ended rental booking grouped by start weekday.">
        <x-slot:actions>
            <flux:button size="sm" variant="ghost" icon="arrow-top-right-on-square" :href="route('flux-admin.active-rentals.index')" class="!rounded-none">Live dashboard</flux:button>
            <flux:button size="sm" variant="ghost" icon="calendar" :href="route('flux-admin.adjust-weekday.index')" class="!rounded-none">Adjust weekday</flux:button>
        </x-slot:actions>
    </x-flux-admin::summary-header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-flux-admin::stat-card label="Active bookings" :value="$bookings->count()" />
        <x-flux-admin::stat-card label="Open-ended items" :value="$bookings->flatMap->rentingBookingItems->whereNull('end_date')->count()" />
        <x-flux-admin::stat-card label="Weekly recurring" :value="'£'.number_format($totalWeekly, 2)" />
    </div>

    @foreach($weekdays as $day)
        @if(isset($byWeekday[$day]) && $byWeekday[$day]->count())
            <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <flux:heading size="md">{{ $day }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-500">{{ $byWeekday[$day]->count() }} bookings · £{{ number_format($byWeekday[$day]->flatMap->rentingBookingItems->whereNull('end_date')->sum('weekly_rent'), 2) }} / week</flux:text>
                </div>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Ref</flux:table.column>
                        <flux:table.column>Customer</flux:table.column>
                        <flux:table.column>Vehicle</flux:table.column>
                        <flux:table.column>Start</flux:table.column>
                        <flux:table.column>Weekly</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($byWeekday[$day] as $b)
                            @php $items = $b->rentingBookingItems->whereNull('end_date'); @endphp
                            <flux:table.row wire:key="abs-{{ $b->id }}">
                                <flux:table.cell class="font-mono">#{{ $b->id }}</flux:table.cell>
                                <flux:table.cell>{{ $b->customer?->name ?: '—' }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $items->pluck('motorbike.reg_no')->filter()->join(', ') ?: '—' }}</flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap">{{ $b->start_date ? \Carbon\Carbon::parse($b->start_date)->format('d M Y') : '—' }}</flux:table.cell>
                                <flux:table.cell>£{{ number_format((float) $items->sum('weekly_rent'), 2) }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:button size="xs" variant="ghost" icon="eye" :href="route('flux-admin.rentals.show', $b->id)" class="!rounded-none">View</flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        @endif
    @endforeach
</div>
