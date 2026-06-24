<div class="space-y-6">
    <div>
        <flux:heading size="xl">Rental operations</flux:heading>
        <flux:text class="mt-1">Same-day rental workflows in Flux Admin — intake, documents, payments, issuance and closing.</flux:text>
    </div>

    @php
        $cards = [
            ['Bookings management', 'Active and inactive rentals — open any booking to run the full lifecycle.', route('flux-admin.bookings-management.index'), 'list-bullet'],
            ['New booking', 'Same-day intake wizard.', route('flux-admin.new-booking.index'), 'plus-circle'],
            ['Active rentals', 'Live dashboard of open items and outstanding invoices.', route('flux-admin.active-rentals.index'), 'truck'],
            ['Due payments', 'Overdue invoices with WhatsApp reminders.', route('flux-admin.rental-due-payments.index'), 'exclamation-triangle'],
            ['Inactive bookings', 'Ended rentals (end date set on item).', route('flux-admin.inactive-bookings.index'), 'archive-box'],
            ['All bookings', 'Historical bookings list.', route('flux-admin.all-bookings.index'), 'clock'],
            ['Adjust weekday', 'Shift a booking\'s invoicing day.', route('flux-admin.adjust-weekday.index'), 'arrow-path'],
            ['Booking invoices', 'All invoices across every booking.', route('flux-admin.booking-invoices.index'), 'document-text'],
            ['Rental pricing', 'Weekly-rate matrix by vehicle class.', route('flux-admin.renting-pricing.index'), 'currency-pound'],
            ['Rentals module', 'Module hub with related links.', route('flux-admin.modules.show', 'rentals'), 'squares-2x2'],
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
