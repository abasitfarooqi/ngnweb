<div class="space-y-6">
    <div>
        <flux:heading size="xl">Rental operations</flux:heading>
        <flux:text class="mt-1">Day-to-day rental workflows. Ticket-level actions (new booking, start/end date changes, closures, issuances) still run through the legacy admin where the multi-step forms live.</flux:text>
    </div>

    @php
        $cards = [
            ['Rentals list', 'Full bookings table with filters, exports and drill-in.', route('flux-admin.rentals.index'), 'list-bullet'],
            ['Active rentals', 'Live dashboard of open items and outstanding invoices.', route('flux-admin.active-rentals.index'), 'truck'],
            ['Due payments', 'Overdue invoices with one-tap WhatsApp reminders.', route('flux-admin.rental-due-payments.index'), 'exclamation-triangle'],
            ['Adjust weekday', 'Shift a booking\'s invoicing day.', route('flux-admin.adjust-weekday.index'), 'arrow-path'],
            ['Booking invoices', 'All invoices across every booking.', route('flux-admin.booking-invoices.index'), 'document-text'],
            ['Rental pricing', 'Weekly-rate matrix by vehicle class.', route('flux-admin.renting-pricing.index'), 'currency-pound'],
            ['Terminate access links', 'Customer-side termination passcodes.', route('flux-admin.rental-terminate-links.index'), 'key'],
            ['New booking (legacy)', 'Multi-step new-booking wizard in classic admin.', url('/admin/page/rentals/bookings/new'), 'arrow-up-right', true],
            ['Bookings management (legacy)', 'Full operational admin for rentals.', url('/admin/page/rentals/bookings'), 'arrow-up-right', true],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($cards as $c)
            <a href="{{ $c[2] }}" @if(! empty($c[4])) target="_blank" @else wire:navigate @endif
                class="group block border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                <div class="flex items-start gap-3">
                    <flux:icon :name="$c[3]" class="size-6 text-blue-600 dark:text-blue-400 shrink-0" />
                    <div class="min-w-0">
                        <div class="font-semibold text-zinc-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 flex items-center gap-2">
                            {{ $c[0] }}
                            @if(! empty($c[4]))
                                <span class="text-xs font-normal text-zinc-400">opens legacy</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $c[1] }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
