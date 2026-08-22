<div class="space-y-6">
    <div>
        <flux:heading size="xl">Rentals</flux:heading>
        <flux:text class="mt-1">Intake, active rentals, ending, pricing and e-bikes — one home for rental operations.</flux:text>
    </div>

    @php
        $cards = [
            
            ['New booking', 'Same-day intake wizard.', route('flux-admin.new-booking.index'), 'plus-circle'],
            ['Active bookings rental', 'Filters, outstanding balances, open booking detail.', route('flux-admin.rentals.index'), 'list-bullet'],
            ['Inactive bookings', 'Ended rentals (end date set on item).', route('flux-admin.inactive-bookings.index'), 'archive-box'],
            ['Inactive pendings payments', 'Ended with balances still outstanding (proceed-anyway).', route('flux-admin.ended-with-pendings.index'), 'exclamation-triangle'],
            ['Rentals referrals', 'Investigate referrals, pending points and free-week rewards.', route('flux-admin.rental-referrals.index'), 'user-plus'],
            ['Weekly follow-up report', 'Monday–Saturday snapshot of invoice and rental history notes for the director.', route('flux-admin.rental-weekly-follow-up-report.index'), 'document-text'],
            ['All bookings', 'Historical bookings list.', route('flux-admin.all-bookings.index'), 'clock'],
            ['E-bike manager', 'Add and edit fleet e-bikes.', route('flux-admin.ebikes.index'), 'bolt'],
            ['Price Adjustment', 'Rental price history and weekly rates by vehicle.', route('flux-admin.motorbike-pricing.index'), 'currency-pound'],
            ['Document expire date', 'Generate and manage customer document upload links.', route('flux-admin.upload-document-links.index'), 'document-text'],
            ['Signature expire date', 'Generate and manage rental agreement signing links.', route('flux-admin.agreement-access.index'), 'pencil-square'],
            ['Terminate / generate link', 'Create and search rental termination signing links.', route('flux-admin.rental-terminate-links.index'), 'link'],
            ['Active rentals overview', 'Live dashboard of open items and outstanding invoices.', route('flux-admin.active-rentals.index'), 'truck'],
            ['Rental Due Whatsapp Reminders', 'Overdue invoices with WhatsApp reminders.', route('flux-admin.rental-due-payments.index'), 'chat-bubble-left-right'],
            ['Renting service videos', 'Upload and manage rental service videos.', route('flux-admin.service-videos.index'), 'video-camera'],
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
