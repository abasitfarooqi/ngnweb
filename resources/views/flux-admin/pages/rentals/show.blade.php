<div>
    @if($flashMessage)
        <div class="mb-4 border px-4 py-3 text-sm font-medium
            {{ $flashType === 'success' ? 'border-emerald-300 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : 'border-red-300 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200' }}">
            {{ $flashMessage }}
        </div>
    @endif

    @php
        $lifecycleBadge = match ($lifecycle) {
            'active' => ['label' => 'Active rental', 'color' => 'emerald'],
            'ended' => ['label' => 'Ended', 'color' => 'zinc'],
            default => ['label' => 'Intake (unposted)', 'color' => 'amber'],
        };
    @endphp

    <x-flux-admin::summary-header
        :title="'Booking #' . $booking->id"
        :subtitle="$booking->customer ? ($booking->customer->first_name . ' ' . $booking->customer->last_name) : 'No customer'"
        :badges="[
            $lifecycleBadge,
            ['label' => ucfirst($booking->state ?? 'N/A'), 'color' => str_contains($booking->state ?? '', 'Issued') ? 'emerald' : (str_contains($booking->state ?? '', 'Await') ? 'amber' : 'zinc')],
        ]"
        :backUrl="route('flux-admin.bookings-management.index')"
        backLabel="Back to bookings"
    >
        <x-slot:actions>
            @if($lifecycle === 'intake')
                @if($missingDocs === 0)
                    <flux:button size="sm" variant="primary" wire:click="activateRental" wire:confirm="Activate this rental for today? All required documents are approved.">
                        Activate rental
                    </flux:button>
                @else
                    <flux:button size="sm" variant="primary" wire:click="$set('activeTab', 'documents')">
                        {{ $missingDocs }} doc(s) pending
                    </flux:button>
                @endif
                <flux:button size="sm" variant="danger" wire:click="abortIntake" wire:confirm="Remove this unposted intake? This cannot be undone.">
                    Abort intake
                </flux:button>
            @elseif($lifecycle === 'active')
                <flux:button size="sm" variant="ghost" wire:click="$set('activeTab', 'closing')">End rental</flux:button>
            @endif
        </x-slot:actions>
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
            <div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Customer</p>
                @if($booking->customer_id)
                    <a href="{{ route('flux-admin.customers.show', $booking->customer_id) }}" wire:navigate
                       class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        View Customer ↗
                    </a>
                @else
                    <p class="text-sm text-zinc-400">—</p>
                @endif
            </div>
        </x-slot:stats>
    </x-flux-admin::summary-header>

    {{-- Tabs --}}
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 mb-6">
        <div class="flex overflow-x-auto border-b border-zinc-200 dark:border-zinc-700">
            @foreach([
                'items'        => 'Booking Items',
                'documents'    => 'Documents',
                'invoices'     => 'Invoices',
                'transactions' => 'Transactions',
                'agreement'    => 'Agreement',
                'charges'      => 'Other Charges',
                'issuance'     => 'Issuance',
                'closing'      => 'Closing',
            ] as $tab => $label)
                <button
                    wire:click="$set('activeTab', '{{ $tab }}')"
                    class="px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition
                        {{ $activeTab === $tab
                            ? 'border-zinc-900 dark:border-white text-zinc-900 dark:text-white'
                            : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
                >
                    {{ $label }}
                    @if($tab === 'closing')
                        <span class="ml-1 text-xs">(6 steps)</span>
                    @endif
                </button>
            @endforeach
        </div>

        <div class="p-0">
            @switch($activeTab)
                @case('items')
                    <livewire:flux-admin.partials.rentals.booking-items-tab :bookingId="$booking->id" :key="'items-' . $booking->id" />
                    @break
                @case('documents')
                    <livewire:flux-admin.partials.rentals.documents-tab :bookingId="$booking->id" :key="'docs-' . $booking->id" />
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
                @case('issuance')
                    <livewire:flux-admin.partials.rentals.issuance-tab :bookingId="$booking->id" :key="'issuance-' . $booking->id" />
                    @break
                @case('closing')
                    <livewire:flux-admin.partials.rentals.closing-tab :bookingId="$booking->id" :key="'closing-' . $booking->id" />
                    @break
            @endswitch
        </div>
    </div>
</div>
