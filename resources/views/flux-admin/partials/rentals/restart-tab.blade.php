<div class="p-4 sm:p-5 space-y-6">
    @if($flashMessage)
        <div class="border px-4 py-3 text-sm font-medium
            {{ $flashType === 'success' ? 'border-emerald-300 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : 'border-red-300 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200' }}">
            {{ $flashMessage }}
        </div>
    @endif

    <div class="border border-amber-400 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-600 px-4 py-3 text-sm text-amber-900 dark:text-amber-200">
        <p class="font-semibold">Accidental close or need to redo steps?</p>
        <p class="mt-1 text-xs">This always keeps <strong>booking #{{ $booking->id }}</strong> — it does not create a new booking. After restart you stay on this same booking.</p>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 space-y-4">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Current booking</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-zinc-500">Lifecycle</p>
                <p class="font-semibold text-zinc-900 dark:text-white">{{ ucfirst($lifecycleStatus) }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500">State</p>
                <p class="font-semibold text-zinc-900 dark:text-white">{{ $booking->state ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500">Posted</p>
                <p class="font-semibold text-zinc-900 dark:text-white">{{ $booking->is_posted ? 'Yes' : 'No (draft)' }}</p>
            </div>
            <div>
                <p class="text-xs text-zinc-500">Motorbike</p>
                <p class="font-semibold font-mono text-zinc-900 dark:text-white">{{ $latestItem?->motorbike?->reg_no ?? '—' }}</p>
            </div>
        </div>
        @if($endedItem)
            <p class="text-xs text-zinc-500">
                Ended {{ \Carbon\Carbon::parse($endedItem->end_date)->format('d M Y') }}
                @if($closing?->collect_time)
                    · collect {{ \Illuminate\Support\Str::of((string) $closing->collect_time)->substr(0, 5) }}
                @endif
            </p>
        @endif
    </div>

    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 space-y-4">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-700 dark:text-zinc-300">Restart options</h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">New start date &amp; time</label>
                <input
                    type="datetime-local"
                    wire:model="restartAt"
                    class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm"
                />
                @error('restartAt') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">What should happen?</label>
                <select
                    wire:model="restartMode"
                    class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm"
                >
                    @if($lifecycleStatus === 'ended')
                        <option value="reopen_ongoing">Reopen as ongoing rental</option>
                    @endif
                    <option value="reset_draft">Reset to draft intake (redo from documents)</option>
                    <option value="resume_documents">Resume at documents step (ongoing)</option>
                    <option value="resume_completed">Resume at agreement / issuance (ongoing)</option>
                </select>
                @error('restartMode') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <ul class="text-xs text-zinc-600 dark:text-zinc-400 space-y-1 list-disc pl-4">
            <li><strong>Reopen as ongoing</strong> — clears end date &amp; closing; restores Completed (or Issued) state.</li>
            <li><strong>Reset to draft</strong> — same booking #{{ $booking->id }}; use Documents tab (not New booking wizard).</li>
            <li><strong>Resume documents</strong> — posted ongoing rental at Awaiting Documents.</li>
            <li><strong>Resume agreement / issuance</strong> — posted ongoing at Completed (ready to sign / issue).</li>
        </ul>

        <flux:button
            size="sm"
            variant="primary"
            wire:click="executeRestart"
            wire:confirm="Apply this restart? Existing payments and records are kept."
            class="!rounded-none"
        >
            Apply restart
        </flux:button>
    </div>

    <div class="border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/40 p-4 text-xs text-zinc-600 dark:text-zinc-400">
        <p class="font-semibold text-zinc-800 dark:text-zinc-200 mb-2">Related tables (this booking)</p>
        <p>
            <code class="text-[11px]">renting_bookings</code> ·
            <code class="text-[11px]">renting_booking_items</code> (active when <code>end_date</code> is null) ·
            <code class="text-[11px]">booking_closing</code> ·
            <code class="text-[11px]">booking_invoices</code> ·
            <code class="text-[11px]">renting_transactions</code> ·
            <code class="text-[11px]">customer_documents</code> ·
            <code class="text-[11px]">customer_agreements</code> ·
            <code class="text-[11px]">booking_issuance_items</code>
        </p>
    </div>
</div>
