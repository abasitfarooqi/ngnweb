<div>
    <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Rentals</h1>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('account.documents', ['tab' => 'rental']) }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:border-brand-red hover:text-brand-red">
                View Documents
            </a>
            <a href="{{ route('account.payments.recurring') }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold uppercase tracking-wider border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:border-brand-red hover:text-brand-red">
                View Recurring Payments
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if ($bookings->isEmpty())
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No rentals yet</h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Get started by browsing our available motorbikes.</p>
            <div class="mt-6">
                <a href="{{ route('account.rentals.browse') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-red hover:bg-red-700">
                    Browse Motorbikes
                </a>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($bookings as $booking)
                @php
                    $activeItem = $booking->rentingBookingItems->whereNull('end_date')->first();
                    $isEnded = $booking->rentingBookingItems->isNotEmpty() && $booking->rentingBookingItems->every(fn ($i) => !empty($i->end_date));
                    $displayState = $isEnded ? 'ENDED' : (string) ($booking->state ?? 'ACTIVE');
                    $isActive = !$isEnded && (bool) $activeItem;
                    $statusClass = match($displayState) {
                        'ACTIVE' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                        'PENDING_RETURN' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                        'COMPLETED', 'ENDED' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                        'CANCELLED' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                        default => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
                    };
                @endphp

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Booking #{{ $booking->id }}
                                </h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $displayState }}
                                </span>
                            </div>
                            @if ($activeItem && $activeItem->motorbike)
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    <strong class="text-gray-900 dark:text-white">{{ $activeItem->motorbike->make }} {{ $activeItem->motorbike->model }}</strong>
                                    ({{ $activeItem->motorbike->reg_no }})
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Start: {{ \Carbon\Carbon::parse($activeItem->start_date)->format('d/m/Y') }}
                                    @if ($activeItem->due_date)
                                        | Due: {{ \Carbon\Carbon::parse($activeItem->due_date)->format('d/m/Y') }}
                                    @endif
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Weekly Rent: £{{ number_format($activeItem->weekly_rent, 2) }}
                                </p>
                            @endif
                            @php
                                $historicalItems = $booking->rentingBookingItems->whereNotNull('end_date');
                            @endphp
                            @if($historicalItems->isNotEmpty())
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Previous bikes in this booking:
                                    @foreach($historicalItems as $item)
                                        @if($item->motorbike)
                                            <span class="inline-block mr-2">{{ $item->motorbike->reg_no }} ({{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') : 'N/A' }} - {{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') : 'N/A' }})</span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center flex-wrap gap-2">
                            <button type="button" wire:click="showPayments({{ $booking->id }})"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Payment History
                            </button>
                            @if ($isActive)
                                <button type="button" wire:click="openExtendModal({{ $booking->id }})"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Extend
                                </button>
                                <button type="button" wire:click="openReturnModal({{ $booking->id }})"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Submit Return Notice
                                </button>
                            @endif
                        </div>
                    </div>

                    @php
                        $invRows = $invoiceDisplayByBooking->get($booking->id, collect());
                        $invPortalMeta = $invoicePortalMetaByBooking->get($booking->id, []);
                        $invBal = $invoiceBalancesByBooking->get($booking->id, ['unpaid_total' => 0, 'unpaid_count' => 0, 'next_due' => null]);
                        $bookingAgreements = $agreementsByBooking->get($booking->id, collect());
                        $bookingOtherCharges = $otherChargesByBooking->get($booking->id, collect());
                        $bookingClosing = $closingByBooking->get($booking->id);
                        $bookingIssuance = $issuanceByBooking->get($booking->id, collect());
                        $bookingMaintenance = $maintenanceByBooking->get($booking->id, collect());
                        $bookingVideos = $serviceVideosByBooking->get($booking->id, collect());
                    @endphp

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700" x-data="{ tab: 'payments' }">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">Booking detail (read only)</p>
                        <div class="flex flex-wrap gap-1 border-b border-gray-200 dark:border-gray-700 pb-2 mb-3 text-xs">
                            <button type="button" @click="tab = 'documents'" :class="tab === 'documents' ? 'text-brand-red border-b-2 border-brand-red' : 'text-gray-500'" class="px-2 py-1">Documents</button>
                            <button type="button" @click="tab = 'payments'" :class="tab === 'payments' ? 'text-brand-red border-b-2 border-brand-red' : 'text-gray-500'" class="px-2 py-1">Payments</button>
                            <button type="button" hidden aria-hidden="true" tabindex="-1" @click="tab = 'issuance'" class="hidden px-2 py-1" :class="tab === 'issuance' ? 'text-brand-red border-b-2 border-brand-red' : 'text-gray-500'">Issuance</button>
                            <button type="button" @click="tab = 'charges'" :class="tab === 'charges' ? 'text-brand-red border-b-2 border-brand-red' : 'text-gray-500'" class="px-2 py-1">Charges</button>
                            <button type="button" @click="tab = 'closing'" :class="tab === 'closing' ? 'text-brand-red border-b-2 border-brand-red' : 'text-gray-500'" class="px-2 py-1">Closing</button>
                            <button type="button" @click="tab = 'workshop'" :class="tab === 'workshop' ? 'text-brand-red border-b-2 border-brand-red' : 'text-gray-500'" class="px-2 py-1">Workshop</button>
                        </div>

                        <div x-show="tab === 'documents'" x-cloak>
                            @if($bookingAgreements->isEmpty())
                                <p class="text-sm text-gray-500">No rental agreement files are on record for this booking yet.</p>
                            @else
                                <ul class="space-y-2 text-sm">
                                    @foreach($bookingAgreements as $ag)
                                        @php
                                            $apath = $ag->file_path ? ltrim(str_replace(['public/', 'storage/'], '', $ag->file_path), '/') : '';
                                            $aurl = $apath && !($ag->sent_private ?? false) ? (\Illuminate\Support\Facades\Storage::disk('public')->exists($apath) ? \Illuminate\Support\Facades\Storage::disk('public')->url($apath) : url('/storage/'.$apath)) : null;
                                        @endphp
                                        <li class="flex flex-wrap items-center justify-between gap-2 border border-gray-200 dark:border-gray-700 p-2">
                                            <span class="text-gray-900 dark:text-white">Rental agreement @if($ag->is_verified)<span class="text-green-600 dark:text-green-400">(verified)</span>@endif</span>
                                            <span class="text-xs text-gray-500">{{ $ag->file_name ?: 'File' }} · {{ optional($ag->created_at)->format('d/m/Y H:i') }}</span>
                                            @if($aurl)
                                                <a href="{{ $aurl }}" target="_blank" rel="noopener" class="text-xs text-brand-red hover:underline">Open file</a>
                                            @elseif($ag->sent_private ?? false)
                                                <span class="text-xs text-gray-400">Held privately — contact us if you need a copy.</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div x-show="tab === 'payments'" x-cloak>
                            @if($isEnded)
                                <p class="text-xs text-gray-500 mb-2">Invoices dated after your rental ended still appear for your records; they are not counted as rent due below.</p>
                            @else
                                <p class="text-xs text-gray-500 mb-2">Only invoices up to the end of this week (Sunday) are listed; later weeks appear when due.</p>
                            @endif
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs mb-3">
                                <div class="border border-gray-200 dark:border-gray-700 p-2">
                                    <p class="text-gray-500">Left to pay (posted, unpaid)</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">£{{ number_format((float) ($invBal['unpaid_total'] ?? 0), 2) }}</p>
                                </div>
                                <div class="border border-gray-200 dark:border-gray-700 p-2">
                                    <p class="text-gray-500">Unpaid invoice count</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ (int) ($invBal['unpaid_count'] ?? 0) }}</p>
                                </div>
                                <div class="border border-gray-200 dark:border-gray-700 p-2">
                                    <p class="text-gray-500">Next due date (unpaid)</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ !empty($invBal['next_due']) ? \Carbon\Carbon::parse($invBal['next_due'])->format('d/m/Y') : '—' }}
                                    </p>
                                </div>
                            </div>
                            @if($invRows->isEmpty())
                                <p class="text-sm text-gray-500">No posted rental invoices to show yet.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                                            <tr>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Invoice</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Tran.</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Invoice date</th>
                                                <th class="px-2 py-2 text-right font-medium text-gray-500 uppercase">Amount</th>
                                                <th class="px-2 py-2 text-right font-medium text-gray-500 uppercase">Paid</th>
                                                <th class="px-2 py-2 text-right font-medium text-gray-500 uppercase">Left to pay</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Paid date</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">State</th>
                                                <th class="px-2 py-2 text-right font-medium text-gray-500 uppercase">Deposit</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Received by</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Posted</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($invRows as $invoice)
                                                @php
                                                    $txns = $invoice->transactions;
                                                    $latest = $txns->first();
                                                    $paidFromTxns = (float) $txns->sum('amount');
                                                    $paidDisplay = $invoice->is_paid ? (float) $invoice->amount : $paidFromTxns;
                                                    $pMeta = $invPortalMeta[$invoice->id] ?? null;
                                                    $leftToPay = $pMeta ? (float) ($pMeta['display_left_to_pay'] ?? max(0, (float) $invoice->amount - $paidDisplay)) : max(0, (float) $invoice->amount - $paidDisplay);
                                                    $postRentalInvoice = (bool) ($pMeta['post_rental'] ?? false);
                                                    $receivedBy = $latest?->user ? trim(($latest->user->first_name ?? '').' '.($latest->user->last_name ?? '')) : '—';
                                                @endphp
                                                <tr>
                                                    <td class="px-2 py-2 text-gray-900 dark:text-white font-medium">{{ $invoice->id }}</td>
                                                    <td class="px-2 py-2 text-gray-700 dark:text-gray-300">
                                                        {{ $latest?->id ?? '—' }}
                                                        @if($txns->count() > 1)
                                                            <span class="block text-gray-400">({{ $txns->count() }} payments)</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-2 py-2 text-gray-700 dark:text-gray-300">{{ $invoice->invoice_date?->format('d/m/Y') ?? '—' }}</td>
                                                    <td class="px-2 py-2 text-right text-gray-900 dark:text-white">£{{ number_format((float) $invoice->amount, 2) }}</td>
                                                    <td class="px-2 py-2 text-right text-gray-900 dark:text-white">£{{ number_format($paidDisplay, 2) }}</td>
                                                    <td class="px-2 py-2 text-right {{ $leftToPay > 0 ? 'text-amber-700 dark:text-amber-300 font-medium' : 'text-gray-700 dark:text-gray-300' }}">£{{ number_format($leftToPay, 2) }}</td>
                                                    <td class="px-2 py-2 text-gray-700 dark:text-gray-300">{{ $invoice->paid_date?->format('d/m/Y') ?? '—' }}</td>
                                                    <td class="px-2 py-2 text-gray-700 dark:text-gray-300">{{ $invoice->state ?? '—' }}@if($postRentalInvoice)<span class="block text-gray-400 normal-case">(record only)</span>@endif</td>
                                                    <td class="px-2 py-2 text-right text-gray-700 dark:text-gray-300">£{{ number_format((float) ($invoice->deposit ?? 0), 2) }}</td>
                                                    <td class="px-2 py-2 text-gray-700 dark:text-gray-300">{{ $receivedBy }}</td>
                                                    <td class="px-2 py-2 text-gray-700 dark:text-gray-300">{{ $latest?->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                                    <td class="px-2 py-2">
                                                        <button type="button" wire:click="downloadInvoice({{ $invoice->id }})" class="text-brand-red hover:underline">PDF</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <div x-show="tab === 'issuance'" x-cloak class="hidden" aria-hidden="true">
                            @if($bookingIssuance->isEmpty())
                                <p class="text-sm text-gray-500">No issuance / handover records yet.</p>
                            @else
                                <ul class="space-y-3 text-sm">
                                    @foreach($bookingIssuance as $iss)
                                        <li class="border border-gray-200 dark:border-gray-700 p-3">
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $iss->bookingItem?->motorbike?->reg_no ?? 'Bike' }} · {{ optional($iss->created_at)->format('d/m/Y H:i') }}</p>
                                            <ul class="mt-2 text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                                <li>Recorded by: {{ $iss->issuedBy ? trim(($iss->issuedBy->first_name ?? '').' '.($iss->issuedBy->last_name ?? '')) : '—' }}</li>
                                                <li>Branch: {{ $iss->issuance_branch ?? '—' }}</li>
                                                <li>Current mileage: {{ $iss->current_mileage ?? '—' }}</li>
                                                <li>Video recorded: {{ $iss->is_video_recorded ? 'Yes' : 'No' }}</li>
                                                <li>Accessories checked: {{ $iss->accessories_checked ? 'Yes' : 'No' }}</li>
                                                <li>
                                                    Insurance checked (AskMID): {{ $iss->is_insured ? 'Yes' : 'No' }}
                                                    — <a href="https://enquiry.navigate.mib.org.uk/checkyourvehicle" target="_blank" rel="noopener noreferrer" class="text-brand-red hover:underline">AskMID vehicle check</a>
                                                </li>
                                                @if($iss->notes)
                                                    <li class="whitespace-pre-line">Notes: {{ $iss->notes }}</li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div x-show="tab === 'charges'" x-cloak>
                            @if($bookingOtherCharges->isEmpty())
                                <p class="text-sm text-gray-500">No additional charges on this booking.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                                            <tr>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Description</th>
                                                <th class="px-2 py-2 text-right font-medium text-gray-500 uppercase">Amount</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Paid</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($bookingOtherCharges as $ch)
                                                <tr>
                                                    <td class="px-2 py-2 text-gray-900 dark:text-white">{{ $ch->description }}</td>
                                                    <td class="px-2 py-2 text-right">£{{ number_format((float) $ch->amount, 2) }}</td>
                                                    <td class="px-2 py-2">{{ ($ch->is_paid ?? false) ? 'Yes' : 'No' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">PCNs are managed separately — see the PCN summary below.</p>
                            @endif
                        </div>

                        <div x-show="tab === 'closing'" x-cloak>
                            @if(!$bookingClosing)
                                <p class="text-sm text-gray-500">No closing checklist has been started for this booking.</p>
                            @else
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    <div class="border border-gray-200 dark:border-gray-700 p-2">
                                        <dt class="text-gray-500">Notice</dt>
                                        <dd class="text-gray-900 dark:text-white mt-1 whitespace-pre-line">{{ $bookingClosing->notice_details ?: '—' }}</dd>
                                        <dd class="text-gray-400 mt-1">{{ $bookingClosing->notice_checked ? 'Confirmed' : 'Not confirmed' }}</dd>
                                    </div>
                                    <div class="border border-gray-200 dark:border-gray-700 p-2">
                                        <dt class="text-gray-500">Collection</dt>
                                        <dd class="text-gray-900 dark:text-white mt-1 whitespace-pre-line">{{ $bookingClosing->collect_details ?: '—' }}</dd>
                                        <dd class="text-gray-400 mt-1">{{ $bookingClosing->collect_date }} {{ $bookingClosing->collect_time }} · {{ $bookingClosing->collect_checked ? 'Confirmed' : 'Not confirmed' }}</dd>
                                    </div>
                                    <div class="border border-gray-200 dark:border-gray-700 p-2">
                                        <dt class="text-gray-500">Steps</dt>
                                        <dd class="text-gray-700 dark:text-gray-300 mt-1 space-x-2">
                                            <span>Damages: {{ $bookingClosing->damages_checked ? '✓' : '—' }}</span>
                                            <span>PCN: {{ $bookingClosing->pcn_checked ? '✓' : '—' }}</span>
                                            <span>Rent: {{ $bookingClosing->pending_checked ? '✓' : '—' }}</span>
                                            <span>Deposit: {{ $bookingClosing->deposit_checked ? '✓' : '—' }}</span>
                                        </dd>
                                    </div>
                                </dl>
                                @php
                                    $closingRentDue = (float) ($invBal['unpaid_total'] ?? 0);
                                    $_pcnClosing = $pcnByBooking->get($booking->id);
                                    $closingPcnOut = $_pcnClosing ? max(0, (float) ($_pcnClosing->total ?? 0) - (float) ($_pcnClosing->paid ?? 0)) : 0;
                                    $closingDeposit = (float) $invRows->max('deposit');
                                @endphp
                                <div class="mt-3 border border-gray-200 dark:border-gray-700 p-2 text-xs space-y-1">
                                    <p class="font-semibold text-gray-700 dark:text-gray-300">Figures (read only)</p>
                                    <p class="text-gray-600 dark:text-gray-400">Pending rent (posted, unpaid): <span class="text-gray-900 dark:text-white">£{{ number_format($closingRentDue, 2) }}</span></p>
                                    <p class="text-gray-600 dark:text-gray-400">PCN outstanding: <span class="text-gray-900 dark:text-white">£{{ number_format($closingPcnOut, 2) }}</span> <span class="text-gray-500">(see PCN summary below)</span></p>
                                    <p class="text-gray-600 dark:text-gray-400">Largest deposit on an invoice: <span class="text-gray-900 dark:text-white">£{{ number_format($closingDeposit, 2) }}</span></p>
                                </div>
                            @endif
                        </div>

                        <div x-show="tab === 'workshop'" x-cloak>
                            <p class="text-xs text-gray-500 mb-2">Maintenance logs linked to this booking.</p>
                            <h5 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Maintenance</h5>
                            @if($bookingMaintenance->isEmpty())
                                <p class="text-sm text-gray-500 mb-3">No maintenance logs found.</p>
                            @else
                                <div class="overflow-x-auto mb-4">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                                            <tr>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Date</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Reg</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Description</th>
                                                <th class="px-2 py-2 text-right font-medium text-gray-500 uppercase">Cost</th>
                                                <th class="px-2 py-2 text-left font-medium text-gray-500 uppercase">Logged by</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bookingMaintenance as $log)
                                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                                    <td class="px-2 py-2">{{ $log->serviced_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                                    <td class="px-2 py-2">{{ $log->motorbike?->reg_no ?? '—' }}</td>
                                                    <td class="px-2 py-2">{{ $log->description }}{{ $log->note ? ' · '.$log->note : '' }}</td>
                                                    <td class="px-2 py-2 text-right">£{{ number_format((float) $log->cost, 2) }}</td>
                                                    <td class="px-2 py-2">{{ $log->user ? trim(($log->user->first_name ?? '').' '.($log->user->last_name ?? '')) : '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <div class="hidden" aria-hidden="true">
                                <h5 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Uploaded videos</h5>
                                @if($bookingVideos->isEmpty())
                                    <p class="text-sm text-gray-500">No videos uploaded.</p>
                                @else
                                    <ul class="space-y-2 text-xs">
                                        @foreach($bookingVideos as $vid)
                                            <li>
                                                <a href="{{ $vid->video_url }}" target="_blank" rel="noopener" class="text-brand-red hover:underline">{{ basename($vid->video_path ?? 'video') }}</a>
                                                @if($vid->recorded_at)
                                                    <span class="text-gray-500"> · {{ \Carbon\Carbon::parse($vid->recorded_at)->format('d/m/Y H:i') }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    @php
                        $pcnRow = $pcnByBooking->get($booking->id);
                        $pcnEntries = $pcnDetailsByBooking->get($booking->id, collect());
                        $pcnTotal = (float) ($pcnRow->total ?? 0);
                        $pcnPaid = (float) ($pcnRow->paid ?? 0);
                        $pcnOutstanding = max(0, $pcnTotal - $pcnPaid);
                        $pcnCaseCount = (int) ($pcnRow->total_count ?? $pcnEntries->count());
                        $pcnOpenCount = (int) ($pcnRow->open_count ?? 0);
                        $pcnAppealedCount = (int) ($pcnRow->appealed_count ?? 0);
                    @endphp
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700" x-data="{ openPcn: false }">
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">PCN Summary (Read Only)</h4>
                            @if($pcnEntries->isNotEmpty())
                                <button type="button" @click="openPcn = !openPcn" class="text-xs text-brand-red hover:underline">
                                    <span x-show="!openPcn">Expand details</span>
                                    <span x-show="openPcn" x-cloak>Hide details</span>
                                </button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-5 gap-2 text-sm">
                            <div class="border border-gray-200 dark:border-gray-700 p-2">
                                <p class="text-xs text-gray-500">Total PCN</p>
                                <p class="font-semibold text-gray-900 dark:text-white">GBP {{ number_format($pcnTotal, 2) }}</p>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 p-2">
                                <p class="text-xs text-gray-500">Paid</p>
                                <p class="font-semibold text-gray-900 dark:text-white">GBP {{ number_format($pcnPaid, 2) }}</p>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 p-2">
                                <p class="text-xs text-gray-500">Outstanding</p>
                                <p class="font-semibold {{ $pcnOutstanding > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                    GBP {{ number_format($pcnOutstanding, 2) }}
                                </p>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 p-2">
                                <p class="text-xs text-gray-500">Cases</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $pcnCaseCount }}</p>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 p-2">
                                <p class="text-xs text-gray-500">Open / Appealed</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $pcnOpenCount }} / {{ $pcnAppealedCount }}</p>
                            </div>
                        </div>

                        @if($pcnEntries->isNotEmpty())
                            <div x-show="openPcn" x-cloak class="mt-3 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th class="px-2 py-2 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Date</th>
                                            <th class="px-2 py-2 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">PCN</th>
                                            <th class="px-2 py-2 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Reg</th>
                                            <th class="px-2 py-2 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                                            <th class="px-2 py-2 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Paid/Owner</th>
                                            <th class="px-2 py-2 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Flags</th>
                                            <th class="px-2 py-2 text-right font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($pcnEntries as $pcn)
                                            <tr>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-300">
                                                    {{ $pcn->date_of_contravention ? \Carbon\Carbon::parse($pcn->date_of_contravention)->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td class="px-2 py-2 text-gray-900 dark:text-white font-medium">{{ $pcn->pcn_number }}</td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-300">{{ $pcn->reg_no }}</td>
                                                <td class="px-2 py-2 {{ $pcn->status_tone }}">{{ $pcn->status_label }}</td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-300">{{ $pcn->paid_by_label }}</td>
                                                <td class="px-2 py-2 text-gray-700 dark:text-gray-300">
                                                    {{ $pcn->is_appealed ? 'Appealed' : '-' }}
                                                    {{ $pcn->is_transferred ? ' · Transferred' : '' }}
                                                </td>
                                                <td class="px-2 py-2 text-right font-semibold text-gray-900 dark:text-white">
                                                    GBP {{ number_format((float) $pcn->amount, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mt-3 text-xs text-gray-500">No PCN records linked to this booking yet.</p>
                        @endif
                    </div>

                    @php $repairReports = $repairReportsByBooking->get($booking->id, collect()); @endphp
                    @if($repairReports->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Workshop Repair Reports</h4>
                            <div class="space-y-2">
                                @foreach($repairReports as $repair)
                                    <div class="flex items-center justify-between gap-2 text-sm border border-gray-200 dark:border-gray-700 p-2">
                                        <div>
                                            <p class="text-gray-900 dark:text-white font-medium">
                                                {{ $repair->motorbike?->reg_no ? 'Bike '.$repair->motorbike->reg_no : 'Repair #'.$repair->id }}
                                            </p>
                                            <p class="text-xs text-gray-500">Report #{{ $repair->id }} · {{ optional($repair->arrival_date)->format('d/m/Y H:i') ?? 'Date N/A' }}</p>
                                        </div>
                                        <button
                                            type="button"
                                            wire:click="downloadRepairReport({{ $repair->id }})"
                                            class="text-brand-red hover:text-red-700 font-medium">
                                            Download PDF
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($showPaymentHistory && $selectedBooking)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4" wire:key="payments-modal">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-4xl w-full shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment History – Booking #{{ $selectedBooking }}</h3>
                    <button type="button" wire:click="closePayments" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @if ($paymentHistory->isEmpty())
                    <p class="text-sm text-gray-600 dark:text-gray-400">No payment history available.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Method</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($paymentHistory as $transaction)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $transaction->transactionType?->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $transaction->paymentMethod?->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">£{{ number_format($transaction->amount, 2) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->bookingInvoice ? 'Invoice #' . $transaction->invoice_id : '–' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="mt-4 flex justify-end">
                    <button type="button" wire:click="closePayments"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showExtendModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4" wire:key="extend-modal">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full shadow-xl">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Extend Rental Period</h3>
                <div class="mb-4">
                    <label for="extendWeeks" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of weeks to extend</label>
                    <input type="number" wire:model="extendWeeks" id="extendWeeks" min="1" max="52"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white">
                    @error('extendWeeks') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeExtendModal"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="button" wire:click="extendRental"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-red hover:bg-red-700">
                        Confirm Extension
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showReturnModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4" wire:key="return-modal">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-lg w-full shadow-xl">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Submit Return Notice</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Please provide your intended return date and any additional notes.</p>
                <div class="mb-4">
                    <label for="returnNotice" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Details</label>
                    <textarea wire:model="returnNotice" id="returnNotice" rows="4" placeholder="e.g., I would like to return the motorbike on 25/03/2026. Please advise on the return process."
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-brand-red focus:border-brand-red dark:bg-gray-700 dark:text-white"></textarea>
                    @error('returnNotice') <span class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeReturnModal"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="button" wire:click="submitReturnNotice"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-brand-red hover:bg-red-700">
                        Submit Notice
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
