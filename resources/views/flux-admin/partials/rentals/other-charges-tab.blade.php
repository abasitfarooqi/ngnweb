<div>
    {{-- Flash message --}}
    @if($flashMessage)
        <div class="mx-4 mt-4 p-3 text-sm font-medium border
            {{ $flashType === 'success' ? 'border-emerald-400 bg-emerald-50 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-700' : 'border-red-400 bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700' }}">
            {{ $flashMessage }}
        </div>
    @endif

    {{-- Add charge form --}}
    <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
        <h4 class="text-xs font-bold uppercase tracking-wide text-zinc-600 dark:text-zinc-400 mb-3">Additional Charges</h4>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-3 italic">Any additional amount on top of rent (e.g. damages). PCN must be updated on /ngn-admin/.</p>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input
                    wire:model="description"
                    type="text"
                    class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                    placeholder="Description of additional charge"
                />
                @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="sm:w-40">
                <input
                    wire:model="amount"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-red"
                    placeholder="Amount (£)"
                />
                @error('amount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <button
                    wire:click="addCharge"
                    class="px-4 py-2 text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition whitespace-nowrap"
                >Add Charge</button>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    @if($charges->isNotEmpty())
        <div class="flex flex-wrap gap-4 px-4 py-3 bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700">
            <div>
                <span class="text-xs text-zinc-500">Total Charges</span>
                <p class="text-sm font-bold text-zinc-900 dark:text-white">£{{ number_format($totalAmount, 2) }}</p>
            </div>
            <div>
                <span class="text-xs text-zinc-500">Paid</span>
                <p class="text-sm font-bold {{ $paidAmount >= $totalAmount ? 'text-emerald-600' : 'text-amber-600' }}">£{{ number_format($paidAmount, 2) }}</p>
            </div>
            <div>
                <span class="text-xs text-zinc-500">Outstanding</span>
                <p class="text-sm font-bold {{ ($totalAmount - $paidAmount) > 0 ? 'text-red-600' : 'text-emerald-600' }}">£{{ number_format($totalAmount - $paidAmount, 2) }}</p>
            </div>
        </div>

        <div class="touch-pan-x overflow-x-auto">
            <div class="min-w-[36rem] md:min-w-0">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>ID</flux:table.column>
                        <flux:table.column>Description</flux:table.column>
                        <flux:table.column>Amount</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>&nbsp;</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($charges as $charge)
                            <flux:table.row wire:key="charge-{{ $charge->id }}" class="{{ $charge->getRawOriginal('is_paid') ? '' : 'bg-amber-50 dark:bg-amber-900/10' }}">
                                <flux:table.cell class="font-medium">#{{ $charge->id }}</flux:table.cell>
                                <flux:table.cell>{{ $charge->description ?? '—' }}</flux:table.cell>
                                <flux:table.cell class="font-semibold">
                                    £{{ $charge->getRawOriginal('amount') ? number_format((float) $charge->getRawOriginal('amount'), 2) : '0.00' }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge :color="$charge->getRawOriginal('is_paid') ? 'emerald' : 'amber'" size="sm">
                                        {{ $charge->getRawOriginal('is_paid') ? 'Paid' : 'Unpaid' }}
                                    </flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if(!$charge->getRawOriginal('is_paid'))
                                        <button
                                            wire:click="markPaid({{ $charge->id }})"
                                            wire:confirm="Confirm payment received for this charge?"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold bg-brand-red hover:opacity-90 text-white transition"
                                        >Mark Paid</button>
                                    @else
                                        <span class="text-xs text-zinc-400">Settled</span>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    @else
        <div class="p-8 text-center">
            <flux:icon name="receipt-percent" variant="outline" class="w-8 h-8 mx-auto text-zinc-400 dark:text-zinc-500 mb-3" />
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No additional charges for this booking.</p>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Use the form above to add charges such as damages.</p>
        </div>
    @endif
</div>
