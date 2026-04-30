<div class="space-y-6">
    <div>
        <flux:heading size="xl">Adjust booking weekday</flux:heading>
        <flux:text class="mt-1">Shift a booking's start date forward to the next occurrence of the chosen weekday. All subsequent invoice dates are re-derived from the new start date.</flux:text>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
        <form wire:submit.prevent="adjust" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <x-flux-admin::field-group label="Booking" :error="$errors->first('selectedBookingId')" required>
                <flux:select wire:model="selectedBookingId" placeholder="— Select booking —">
                    @foreach($bookings as $b)
                        <flux:select.option value="{{ $b->id }}">#{{ $b->id }} · {{ $b->customer }} · {{ $b->current_weekday }} ({{ $b->start_date }})</flux:select.option>
                    @endforeach
                </flux:select>
            </x-flux-admin::field-group>
            <x-flux-admin::field-group label="Move to weekday" :error="$errors->first('targetWeekday')" required>
                <flux:select wire:model="targetWeekday">
                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d)
                        <flux:select.option value="{{ $d }}">{{ $d }}</flux:select.option>
                    @endforeach
                </flux:select>
            </x-flux-admin::field-group>
            <flux:button type="submit" variant="primary" icon="arrow-path" class="!rounded-none">Shift booking</flux:button>
        </form>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Booking</flux:table.column>
                <flux:table.column>Customer</flux:table.column>
                <flux:table.column>Start date</flux:table.column>
                <flux:table.column>Weekday</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($bookings as $b)
                    <flux:table.row wire:key="aw-{{ $b->id }}">
                        <flux:table.cell class="font-mono text-sm">#{{ $b->id }}</flux:table.cell>
                        <flux:table.cell>{{ $b->customer }}</flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">{{ $b->start_date }}</flux:table.cell>
                        <flux:table.cell>{{ $b->current_weekday }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="4" class="text-center py-8 text-zinc-500">No active bookings.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
