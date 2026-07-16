<div class="space-y-6">
    <div>
        <flux:heading size="xl">Rentals</flux:heading>
        <flux:text class="mt-1">Intake, active rentals, ending, pricing and e-bikes — one home for rental operations.</flux:text>
    </div>

    @php
        $cards = [
            ['New booking', 'Same-day intake wizard.', route('flux-admin.new-booking.index'), 'plus-circle'],
            ['Repair rental availability', 'Fix bikes missing from New booking select (pricing, registration, MOT/tax, stuck rentals).', route('flux-admin.backpack.motorbike-available.index'), 'wrench-screwdriver'],
            ['Active bookings rental', 'Filters, outstanding balances, open booking detail.', route('flux-admin.rentals.index'), 'list-bullet'],
            ['Inactive bookings', 'Ended rentals (end date set on item).', route('flux-admin.inactive-bookings.index'), 'archive-box'],
            ['Inactive pendings payments', 'Ended with balances still outstanding (proceed-anyway).', route('flux-admin.ended-with-pendings.index'), 'exclamation-triangle'],
            ['All bookings', 'Historical bookings list.', route('flux-admin.all-bookings.index'), 'clock'],
            ['E-bike manager', 'Add and edit fleet e-bikes.', route('flux-admin.ebikes.index'), 'bolt'],
            ['Active rentals overview', 'Live dashboard of open items and outstanding invoices.', route('flux-admin.active-rentals.index'), 'truck'],
            ['Due payments', 'Overdue invoices with WhatsApp reminders.', route('flux-admin.rental-due-payments.index'), 'banknotes'],
            ['Rental pricing', 'Weekly rates by vehicle — search by reg when adding.', route('flux-admin.renting-pricing.index'), 'currency-pound'],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($cards as $c)
            <a href="{{ $c[2] }}" wire:navigate
                class="group block border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                <div class="flex items-start gap-3">
                    <flux:icon :name="$c[3]" class="size-6 text-blue-600 dark:text-blue-400 shrink-0" />
                    <div class="min-w-0">
                        <div class="font-semibold text-zinc-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $c[0] }}</div>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $c[1] }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
