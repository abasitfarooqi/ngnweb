<div>
    <x-flux-admin::summary-header
        :title="'Booking #' . $booking->id"
        :subtitle="$booking->customer ? ($booking->customer->first_name . ' ' . $booking->customer->last_name) : 'No customer'"
        :badges="[
            ['label' => ucfirst($booking->state ?? 'N/A'), 'color' => $booking->state === 'active' ? 'green' : 'zinc'],
            ['label' => $booking->is_posted ? 'Posted' : 'Draft', 'color' => $booking->is_posted ? 'green' : 'amber'],
        ]"
        :backUrl="route('flux-admin.rentals.index')"
        backLabel="Back to Rentals"
    >
        <x-slot:stats>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Start Date</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $booking->start_date?->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Due Date</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $booking->due_date?->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Deposit</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">£{{ number_format($booking->deposit, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Items</p>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $booking->rentingBookingItems->count() }}</p>
            </div>
        </x-slot:stats>
    </x-flux-admin::summary-header>

    {{-- Tabs --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 mb-6">
        <div class="flex overflow-x-auto border-b border-zinc-200 dark:border-zinc-700">
            @foreach(['items' => 'Booking Items', 'invoices' => 'Invoices', 'transactions' => 'Transactions', 'agreement' => 'Agreement', 'charges' => 'Other Charges'] as $tab => $label)
                <button
                    wire:click="$set('activeTab', '{{ $tab }}')"
                    class="px-5 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition {{ $activeTab === $tab ? 'border-zinc-900 dark:border-white text-zinc-900 dark:text-white' : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="p-0">
            @switch($activeTab)
                @case('items')
                    <livewire:flux-admin.partials.rentals.booking-items-tab :bookingId="$booking->id" :key="'items-' . $booking->id" />
                    @break
                @case('invoices')
                    <livewire:flux-admin.partials.rentals.invoices-tab :bookingId="$booking->id" :key="'invoices-' . $booking->id" />
                    @break
                @case('transactions')
                    <livewire:flux-admin.partials.rentals.transactions-tab :bookingId="$booking->id" :key="'transactions-' . $booking->id" />
                    @break
                @case('agreement')
                    <livewire:flux-admin.partials.rentals.agreement-tab :bookingId="$booking->id" :key="'agreement-' . $booking->id" />
                    @break
                @case('charges')
                    <livewire:flux-admin.partials.rentals.other-charges-tab :bookingId="$booking->id" :key="'charges-' . $booking->id" />
                    @break
            @endswitch
        </div>
    </div>
</div>
