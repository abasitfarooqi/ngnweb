<div>
    {{-- Flash message --}}
    @if($flashMessage)
        <div class="mx-4 mt-4 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700' : 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700' }}">
            {{ $flashMessage }}
        </div>
    @endif

    <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Closing Contract</h3>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Complete all 6 steps below before the booking can be formally closed.</p>
    </div>

    @php
        $stepClass = 'w-7 h-7 flex items-center justify-center text-white font-bold text-sm flex-shrink-0';
        $doneColor = 'bg-emerald-600';
        $pendingColor = 'bg-zinc-500';
    @endphp

    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">

        {{-- ════════════════════════════════════════ --}}
        {{-- STEP 1 — Notice Period --}}
        {{-- ════════════════════════════════════════ --}}
        <div class="p-4 md:p-5">
            <div class="flex items-start gap-3">
                <div class="{{ $stepClass }} {{ $noticeChecked ? $doneColor : $pendingColor }}">1</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-zinc-900 dark:text-white mb-2">Notice Period</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Details</label>
                            <input
                                wire:model="noticeDetails"
                                type="text"
                                class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                                placeholder="e.g. Customer called on 12 Jun, motorbike to be handed over..."
                            />
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model="noticeChecked" type="checkbox" class="w-5 h-5 accent-emerald-600" />
                                <span class="text-sm text-zinc-700 dark:text-zinc-300">Notice period confirmed</span>
                            </label>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="saveNoticePeriod"
                        wire:loading.attr="disabled"
                        class="px-4 py-1.5 text-xs font-bold bg-zinc-800 hover:bg-zinc-700 dark:bg-zinc-200 dark:hover:bg-white text-white dark:text-zinc-900 transition disabled:opacity-50"
                    >CHECK</button>
                    <p class="text-xs text-zinc-400 italic mt-1.5">Example: <em>"Received call from customer — motorbike to be handed over on 12-Jun-2025."</em></p>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- STEP 2 — Collect Motorbike --}}
        {{-- ════════════════════════════════════════ --}}
        <div class="p-4 md:p-5">
            <div class="flex items-start gap-3">
                <div class="{{ $stepClass }} {{ $collectChecked ? $doneColor : $pendingColor }}">2</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-zinc-900 dark:text-white mb-2">Collect Motorbike</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                        <div class="lg:col-span-2">
                            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Details</label>
                            <input
                                wire:model="collectDetails"
                                type="text"
                                class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                                placeholder="Enter any details"
                            />
                        </div>
                        <div>
                            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Collect Date</label>
                            <input
                                wire:model="collectDate"
                                type="date"
                                class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                            />
                        </div>
                        <div>
                            <label class="block text-xs text-zinc-500 dark:text-zinc-400 mb-1">Collect Time</label>
                            <input
                                wire:model="collectTime"
                                type="time"
                                class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                            />
                        </div>
                    </div>
                    @if($pendingTotal > 0)
                        <div class="border border-amber-300 bg-amber-50 dark:bg-amber-950 dark:border-amber-700 p-3 text-sm mb-3">
                            <p class="font-medium text-amber-800 dark:text-amber-300">Outstanding £{{ number_format($pendingTotal, 2) }} (due on/before collect date)</p>
                            <ul class="mt-1 text-amber-700 dark:text-amber-400 text-xs space-y-0.5">
                                <li>Rent (outstanding on invoices): £{{ number_format($pendingRent, 2) }}</li>
                                <li>Other charges: £{{ number_format($pendingAdditional, 2) }}</li>
                                <li>Open PCN: £{{ number_format($pcnTotal, 2) }}</li>
                            </ul>
                            <p class="mt-2 text-xs text-amber-700 dark:text-amber-400">Future invoices after collect date are removed on end and do not count here. Proceed anyway emails enquiries + you.</p>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer mb-3">
                            <input wire:model="proceedAnyway" type="checkbox" class="w-5 h-5 accent-amber-600" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">Proceed anyway — I accept responsibility for collecting without clearing balances</span>
                        </label>
                    @endif
                    <div class="flex items-center gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="collectChecked" type="checkbox" class="w-5 h-5 accent-emerald-600" />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">Motorbike collected/returned</span>
                        </label>
                    </div>
                    <button
                        type="button"
                        wire:click="saveCollectMotorbike"
                        wire:loading.attr="disabled"
                        wire:target="saveCollectMotorbike"
                        class="px-4 py-1.5 text-xs font-bold bg-zinc-800 hover:bg-zinc-700 dark:bg-zinc-200 dark:hover:bg-white text-white dark:text-zinc-900 transition disabled:opacity-50"
                    >CHECK</button>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- STEP 3 — Damages / Additional Cost --}}
        {{-- ════════════════════════════════════════ --}}
        <div class="p-4 md:p-5">
            <div class="flex items-start gap-3">
                <div class="{{ $stepClass }} {{ $damagesChecked ? $doneColor : $pendingColor }}">3</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-zinc-900 dark:text-white mb-2">Damages / Additional Cost</p>
                    <div class="flex flex-wrap gap-4 mb-3 text-sm">
                        <div class="bg-zinc-100 dark:bg-zinc-800 px-3 py-2 border border-zinc-200 dark:border-zinc-700">
                            <span class="text-xs text-zinc-500">Total Additional Charges</span>
                            <p class="font-bold text-zinc-900 dark:text-white">£{{ number_format($totalAdditional, 2) }}</p>
                        </div>
                        <div class="bg-zinc-100 dark:bg-zinc-800 px-3 py-2 border border-zinc-200 dark:border-zinc-700">
                            <span class="text-xs text-zinc-500">Paid</span>
                            <p class="font-bold {{ $paidAdditional >= $totalAdditional ? 'text-emerald-600' : 'text-red-600' }}">£{{ number_format($paidAdditional, 2) }}</p>
                        </div>
                    </div>
                    @if($totalAdditional > 0 && $paidAdditional < $totalAdditional)
                        <p class="text-xs text-amber-600 dark:text-amber-400 mb-3">⚠ Outstanding additional charges must be paid via the <strong>Charges</strong> tab before ticking this step.</p>
                    @endif
                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                        <input wire:model="damagesChecked" type="checkbox" class="w-5 h-5 accent-emerald-600"
                            {{ $totalAdditional > 0 && $paidAdditional < $totalAdditional ? 'disabled' : '' }} />
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">All additional charges cleared</span>
                    </label>
                    <button
                        type="button"
                        wire:click="saveDamagesCost"
                        wire:loading.attr="disabled"
                        class="px-4 py-1.5 text-xs font-bold bg-zinc-800 hover:bg-zinc-700 dark:bg-zinc-200 dark:hover:bg-white text-white dark:text-zinc-900 transition disabled:opacity-50"
                    >CHECK</button>
                    <p class="text-xs text-zinc-400 italic mt-1.5">All additional charges are from the <em>Charges</em> tab and must be paid there.</p>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- STEP 4 — PCN Pendings --}}
        {{-- ════════════════════════════════════════ --}}
        <div class="p-4 md:p-5">
            <div class="flex items-start gap-3">
                <div class="{{ $stepClass }} {{ $pcnChecked ? $doneColor : $pendingColor }}">4</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-zinc-900 dark:text-white mb-2">PCN Pendings</p>
                    <div class="flex flex-wrap gap-4 mb-3 text-sm">
                        <div class="bg-zinc-100 dark:bg-zinc-800 px-3 py-2 border border-zinc-200 dark:border-zinc-700">
                            <span class="text-xs text-zinc-500">PCN Outstanding</span>
                            <p class="font-bold {{ $pcnTotal > 0 ? 'text-red-600' : 'text-emerald-600' }}">£{{ number_format($pcnTotal, 2) }}</p>
                        </div>
                    </div>
                    @if($pcnTotal > 0)
                        <p class="text-xs text-amber-600 dark:text-amber-400 mb-3">
                            ⚠ Outstanding PCN must be cleared via <strong>/ngn-admin/pcn-case/</strong> (search by reg number).
                        </p>
                    @endif
                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                        <input wire:model="pcnChecked" type="checkbox" class="w-5 h-5 accent-emerald-600" />
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">All PCN pendings cleared</span>
                    </label>
                    <button
                        type="button"
                        wire:click="savePcnPendings"
                        wire:loading.attr="disabled"
                        class="px-4 py-1.5 text-xs font-bold bg-zinc-800 hover:bg-zinc-700 dark:bg-zinc-200 dark:hover:bg-white text-white dark:text-zinc-900 transition disabled:opacity-50"
                    >CHECK</button>
                    <p class="text-xs text-zinc-400 italic mt-1.5">If PCN is missing it will not reflect here. Search for the registration on the PCN page.</p>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- STEP 5 — Pending Rent --}}
        {{-- ════════════════════════════════════════ --}}
        <div class="p-4 md:p-5">
            <div class="flex items-start gap-3">
                <div class="{{ $stepClass }} {{ $pendingChecked ? $doneColor : $pendingColor }}">5</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-zinc-900 dark:text-white mb-2">Pending Rent</p>
                    <div class="flex flex-wrap gap-4 mb-3 text-sm">
                        <div class="bg-zinc-100 dark:bg-zinc-800 px-3 py-2 border border-zinc-200 dark:border-zinc-700">
                            <span class="text-xs text-zinc-500">Unpaid Invoices (due)</span>
                            <p class="font-bold {{ $pendingRent > 0 ? 'text-red-600' : 'text-emerald-600' }}">£{{ number_format($pendingRent, 2) }}</p>
                        </div>
                    </div>
                    @if($pendingRent > 0)
                        <p class="text-xs text-amber-600 dark:text-amber-400 mb-3">⚠ £{{ number_format($pendingRent, 2) }} pending rent must be cleared on the <strong>Invoices</strong> tab first.</p>
                    @endif
                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                        <input wire:model="pendingChecked" type="checkbox" class="w-5 h-5 accent-emerald-600" />
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">No pending rent outstanding</span>
                    </label>
                    <button
                        type="button"
                        wire:click="savePendingRent"
                        wire:loading.attr="disabled"
                        class="px-4 py-1.5 text-xs font-bold bg-zinc-800 hover:bg-zinc-700 dark:bg-zinc-200 dark:hover:bg-white text-white dark:text-zinc-900 transition disabled:opacity-50"
                    >CHECK</button>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- STEP 6 — Deposit Return --}}
        {{-- ════════════════════════════════════════ --}}
        <div class="p-4 md:p-5">
            <div class="flex items-start gap-3">
                <div class="{{ $stepClass }} {{ $depositChecked ? $doneColor : $pendingColor }}">6</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-zinc-900 dark:text-white mb-2">Deposit Return</p>
                    <div class="flex flex-wrap gap-4 mb-3 text-sm">
                        <div class="bg-zinc-100 dark:bg-zinc-800 px-3 py-2 border border-zinc-200 dark:border-zinc-700">
                            <span class="text-xs text-zinc-500">Booking Deposit</span>
                            <p class="font-bold text-zinc-900 dark:text-white">£{{ number_format($booking->deposit ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer mb-3">
                        <input wire:model="depositChecked" type="checkbox" class="w-5 h-5 accent-emerald-600" />
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">Deposit returned to customer</span>
                    </label>
                    <div class="mb-3 max-w-2xl">
                        <label class="mb-1 block text-xs font-semibold text-zinc-700 dark:text-zinc-300">Deposit return notes / reason of deduction</label>
                        <textarea
                            wire:model="depositReturnNotes"
                            rows="3"
                            class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-emerald-600 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white"
                            placeholder="Explain the deposit return or any deduction reason shown in the customer email."
                        ></textarea>
                        @error('depositReturnNotes') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <button
                        type="button"
                        wire:click="saveDepositReturn"
                        wire:loading.attr="disabled"
                        class="px-4 py-1.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition disabled:opacity-50"
                    >RETURN</button>
                    <p class="text-xs text-zinc-400 italic mt-1.5">Deposit can only be returned at least <strong>15 days</strong> after the motorbike is handed over.</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Summary bar --}}
    <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-700 flex flex-wrap gap-2">
        @foreach([
            ['label' => 'Notice', 'checked' => $noticeChecked],
            ['label' => 'Collection', 'checked' => $collectChecked],
            ['label' => 'Damages', 'checked' => $damagesChecked],
            ['label' => 'PCN', 'checked' => $pcnChecked],
            ['label' => 'Rent', 'checked' => $pendingChecked],
            ['label' => 'Deposit', 'checked' => $depositChecked],
        ] as $step)
            <div class="flex items-center gap-1.5 text-xs {{ $step['checked'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-400 dark:text-zinc-500' }}">
                @if($step['checked'])
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" clip-rule="evenodd"/></svg>
                @else
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                @endif
                {{ $step['label'] }}
            </div>
        @endforeach
    </div>
</div>
